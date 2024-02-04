<?php

/**
 * @package ChatController for Customer
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 26-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\OpenAI\Services\{
    ChatService
};

class ChatController extends Controller
{
    /**
     * Chat Service
     *
     * @var object
     */
    protected $chatService;

    /**
     * Constructor
     * 
     * @param ChatService $chatService
     */
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    /**
     * Store Chat via service
     * @param Request $request
     *
     * @return [type]
     */
    public function saveChat($chat)
    {
        return $this->chatService->save($chat);
    }

    /**
     * View Chat history
     * @param mixed $id
     *
     * @return [type]
     */
    public function history($id)
    {
       return $this->chatService->chatById($id);
    }

    /**
     * Delete chat
     * @param Request $request
     *
     * @return [type]
     */
    public function delete(Request $request)
    {
        $response = ['status' => 'error', 'message' => __('The :x does not exist.', ['x' => __('Chat Conversation')])];

        if ($this->chatService->delete($request->chatId)) {
            $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Chat')])];
        }

        return response()->json($response);
    }

    /**
     * update chat
     * @param Request $request
     *
     * @return [type]
     */
    public function update(Request $request)
    {
        return $this->chatService->update($request->all());
    }

}


