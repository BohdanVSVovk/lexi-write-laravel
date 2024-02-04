<?php

/**
 * @package OpenAiController for Admin
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 26-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Api\V1\User;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ModelTraits\Filterable;
use Modules\OpenAI\Entities\{
    Chat, ChatConversation
};
use Modules\OpenAI\Http\Controllers\Customer\CodeController;

use Modules\OpenAI\Http\Resources\ContentResource;
use Modules\OpenAI\Services\{
    UseCaseTemplateService,
    ContentService,
    ImageService,
    CodeService,
    ChatService
};
use Modules\OpenAI\Entities\OpenAI;
use Modules\OpenAI\Http\Resources\{
    ChatConversationResource,
    ChatResource
};

class OpenAIController extends Controller
{
    /**
     * Use Filtable trait.
     */
    use Filterable;

    /**
     * Content Service
     *
     * @var object
     */
    protected $contentService;

    /**
     * Constructor
     *
     * @param ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * List of all content
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $configs        = $this->initialize([], $request->all());
        $contentServices = $this->contentService;
        $contents = $contentServices->model()->orderBy("id", "desc");

        if (auth('api')->user()->role()->type !== 'admin') {
            $contents = $contents->where('user_id', auth('api')->user()->id);
        }

        if (count(request()->query()) > 0) {
            $contents = $contents->filter();
        }

        $contents = $contents->paginate($configs['rows_per_page']);
        $responseData = ContentResource::collection($contents)->response()->getData(true);
        return $this->response($responseData);
    }

     /**
      * Content view
      *
      * @param mixed $slug
      * @return JsonResponse
      */
    public function view($slug)
    {
        $contents = $this->contentService->contentBySlug($slug);
        if ($contents) {
            return $this->okResponse(new ContentResource($contents));
        }

        return $this->notFoundResponse([], __('No :x found.', ['x' => __('Content')]));
    }

    /**
     * Content version
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, $slug)
    {
        $contents = $this->contentService->model();
        $content = $contents->where('slug', $slug)->whereNull('parent_id')->first();

        if (empty($content)) {
            return $this->notFoundResponse([], __('No :x found.', ['x' => __('Content')]));
        }

        if ($this->contentService->createVersion($content, $request->only('content'))) {
            $content = $this->contentService->model()->where('slug', $slug)->latest()->first();
            return $this->okResponse(new ContentResource($content), __('Content Updated successfully'));
        }
    }

    /**
     * Content delete
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        if (!is_numeric($id)) {
            return $this->forbiddenResponse([], __('Invalid Request!'));
        }

        $delete =  json_decode(json_encode($this->contentService->delete($id)));
        return $delete->original->status == 'fail' ? $this->badRequestResponse([], __($delete->original->message)) : $this->okResponse([], __($delete->original->message));
    }
    /**
     * Ask to the API
     *
     * @param Request $request
     * @param ContentService $contentService
     * @return JsonResponse
     */
    public function ask(Request $request, ContentService $contentService)
    {
        if (!subscription('isAdminSubscribed')) {
            $validation = subscription('isValidSubscription', auth()->user()->id, 'word', $request->useCase);
            $subscription = subscription('getUserSubscription', auth()->user()->id);

            if ($validation['status'] == 'fail') {
                return $this->unprocessableResponse([
                    'response' => $validation['message'],
                    'status' => 'failed',
                ]);
            }
        }

        $request = app('Modules\OpenAI\Http\Requests\ContentStoreRequest')->safe();
        $useCase = $contentService->useCasebySlug($request->useCase);
        $templateService = new UseCaseTemplateService($useCase->prompt);
        $templateService->setVariables(json_decode($request->questions, true));
        $model = preference('ai_model');

        try {
            if (in_array($model, $contentService->chatModel())) {
                $result = OpenAI::contentCreate([
                    'model' => $model,
                    'messages' => [
                        [
                            "role" => "user",
                            "content" => $templateService->render() .' '. 'The writing language must be in'.  $request->language. ' '. 'and please keep the tone ' .' '. $request->tone
                        ],
                    ],
                    'temperature' => (float) $request->temperature,
                    'n' => (int) $request->variant,
                ]); 
            } else {
                    $result = OpenAI::completions([
                        'prompt' => $templateService->render() .' '. 'The writing language must be in'.  $request->language. ' '. 'and please keep the tone ' .' '. $request->tone,
                        'temperature' => (float) $request->temperature,
                        'n' => (int) $request->variant,
                    ]);
                }
           
            if ($result) {
                $response = $contentService->prepareData($request->all(), $useCase->id, $templateService->render(), $result);

                $response->words = subscription('tokenToWord', $response->usage->totalTokens);

                if (!subscription('isAdminSubscribed')) {
                    subscription('usageIncrement', $subscription->id, 'word', $response->words);
                }

                return $this->successResponse($response);
            }

        } catch (Exception $e) {
            $response = $e->getMessage();
            $data = [
                'response' => $response,
                'status' => 'failed',
            ];

            return $this->unprocessableResponse($data);
        }
    }

    /**
     * Image creation from promt
     * @param Request $request
     * @param ImageService $imageService
     *
     * @return [type]
     */
    public function image(Request $request, ImageService $imageService)
    {
        if (!subscription('isAdminSubscribed')) {
            $validation = subscription('isValidSubscription', auth()->user()->id, 'image');

            $subscription = subscription('getUserSubscription', auth()->user()->id);

            if ($validation['status'] == 'fail') {
                return $this->unprocessableResponse([
                    'response' => $validation['message'],
                    'status' => 'failed',
                ]);
            }

            if (!subscription('isValidResolution', auth()->user()->id, (int) $request->resulation)) {
                return $this->unprocessableResponse([
                    'response' => __('This resolution is not available in your plan.'),
                    'status' => 'failed',
                ]);
            }
        }

        try {
            $imageUrls = $imageService->createImage($request->all());
            if (isset($imageUrls['status']) && $imageUrls['status'] == 'error' || is_null($imageUrls)) {
                return $this->unprocessableResponse($imageUrls);
            } else {
                if (!subscription('isAdminSubscribed')) {
                    subscription('usageIncrement', $subscription->id, 'image', $request->variant);
                }
                return $this->successResponse($imageUrls);
            }
        } catch(Exception $e) {
            $response = $e->getMessage();
            $data = [
                'response' => $response,
                'status' => 'failed',
            ];
            return $this->unprocessableResponse($data);
        }
    }

     /**
     * Code creation from promt
     * @param Request $request
     * @param CodeService $codeService
     *
     * @return [type]
     */
    public function code(Request $request, CodeService $codeService, CodeController $codeController)
    {

        if (!subscription('isAdminSubscribed')) {
            $validation = subscription('isValidSubscription', auth()->user()->id, 'word');
            $subscription = subscription('getUserSubscription', auth()->user()->id);

            if ($validation['status'] == 'fail') {
                return $this->unprocessableResponse([
                    'response' => $validation['message'],
                    'status' => 'failed',
                ]);
            }
        }

        try {
            $code = $codeService->createCode($request->all());

            if (!empty($code['error'])) {
                $message = '';

                if ($code['error']['message'] != '') {
                    $message = $code['error']['message'];
                } else if ($code['error']['code']) {
                    $message = str_replace('_', ' ', $code['error']['code']);
                }

                return $this->unprocessableResponse([], $message);
            }

            $words = subscription('tokenToWord', $code['usage']['total_tokens']);

            if (!subscription('isAdminSubscribed')) {
                subscription('usageIncrement', $subscription->id, 'word', $words);
            }

            $response = $codeController->saveCode($code);
            $response['usage']['words'] = $words;

            return $this->successResponse($response);
        } catch(Exception $e) {
            $response = $e->getMessage();
            $data = [
                'response' => $response,
                'status' => 'failed',
            ];
            return $this->unprocessableResponse($data);
        }
    }

    /**
     * Chat
     * 
     * @param Request $request
     * @param ChatService $chatService
     * @param ChatController $chatController
     *
     * @return array
     */
    public function chat(Request $request, ChatService $chatService, ChatController $chatController)
    {
        if (!subscription('isAdminSubscribed')) {
            $validation = subscription('isValidSubscription', auth()->user()->id, 'word');
            $subscription = subscription('getUserSubscription', auth()->user()->id);

            if ($validation['status'] == 'fail') {
                return $this->unprocessableResponse([
                    'response' => $validation['message'],
                    'status' => 'failed',
                ]);
            }
        }

        try {
            $chat = $chatService->createChat($request->all());

            if (empty($chat['error'])) {
                $words = subscription('tokenToWord', $chat['usage']['total_tokens']);
                if (!subscription('isAdminSubscribed')) {
                    subscription('usageIncrement', $subscription->id, 'word', $words);
                }

                $response = $chatController->saveChat($chat);
                $response['usage']['words'] = $words;

                return $this->successResponse($response);
            }

            return $this->unprocessableResponse([
                'response' => $chat['error']['message'],
                'status' => 'failed',
            ]);
        } catch(Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Chat Conversation
     *
     * @param Request $request
     * @return array
     */
    public function chatConversation(Request $request)
    {
        $configs = $this->initialize([], $request->all());
        $chatConversation = ChatConversation::with(['user', 'user.metas'])->where('user_id', auth('api')->user()->id)->orderBy('id', 'DESC');

        if (!$chatConversation->count()) {
            return $this->notFoundResponse();
        }

        return $this->response([
            'data' => ChatConversationResource::collection($chatConversation->paginate($configs['rows_per_page'])),
            'pagination' => $this->toArray($chatConversation->paginate($configs['rows_per_page'])->appends($request->all()))
        ]);
    }

    /**
     * View Chat history
     * @param Request $request
     * @param int $chatConversationId
     * @return array
     */
    public function history(Request $request, $chatConversationId)
    {
        $configs = $this->initialize([], $request->all());
        $chat = Chat::with(['user', 'chatBot', 'user.metas'])->where('chat_conversation_id', $chatConversationId)->orderBy('id', 'DESC');

        if (!$chat->count()) {
            return $this->notFoundResponse();
        }

        return $this->response([
            'data' => ChatResource::collection($chat->paginate($configs['rows_per_page'])),
            'pagination' => $this->toArray($chat->paginate($configs['rows_per_page'])->appends($request->all()))
        ]);
    }


}


