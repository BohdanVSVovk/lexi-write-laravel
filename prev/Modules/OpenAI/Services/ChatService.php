<?php

/**
 * @package ChatService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 30-05-2023
 */

 namespace Modules\OpenAI\Services;


use Illuminate\Support\Facades\DB;
use Modules\OpenAI\Entities\{
    Chat,
    ChatBot,
    ChatConversation,
};

use Exception;

 class ChatService
 {
    /**
     * @var [type]
     */
    protected $formData;
    protected $promt;


    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($formData = null, $promt = null)
    {
        $this->formData = $formData;
        $this->promt = $promt;
    }

    /**
     * URL of API
     *
     * @return string
     */
    public function getUrl()
    {
        return config('openAI.chatUrl');
    }

    /**
     * URL of API
     *
     * @return string
     */
    public function getModel()
    {
        return config('openAI.chatModel');
    }

    /**
     * Token of chat module
     *
     * @return string
     */
    public function getToken()
    {
        return preference('max_token_length');
    }

    /**
     * get Api Key
     *
     * @return string
     */
    public function aiKey()
    {
        return preference('openai');
    }

    /**
     * Client
     *
     * @return \OpenAI\Client
     */
    public function client()
    {
        return \OpenAI::client($this->aiKey());
    }

    /**
     * Image create
     *
     * @param mixed $data
     * @return array
     */
    public function createChat($data)
    {
        $this->formData = $data;
        return $this->preparePromt();
    }

    /**
     * Assistant
     *
     * @param string|null $code
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function assistant($code = null)
    {
        $chatBot = ChatBot::query();
        if ($code) {
            $chatBot->where(['code' => $code]);
        }
        return $chatBot->first();
    }

    /**
     * Prepare promt
     *
     * @return array
     */
    public function preparePromt()
    {
        $this->promt = ([
            'model' => $this->getModel(),
            'messages' => [
                [
                    "role" => "system",
                    "content" => $this->assistant()->promt,
                ],
                [
                    "role" => "user",
                    "content" => $this->formData['promt'],
                ],
            ],
            'temperature' => 1,
            'max_tokens' => (int) $this->getToken(),
            "temperature" => 1,
            "top_p" => 1,
            "n" => 1,
            "stream" => false,
            "max_tokens" => 250,
            "presence_penalty" => 0,
            "frequency_penalty" => 0
        ]);

        return $this->getResponse();
    }

    /**
     * Get Response
     *
     * @return array
     */
    private function getResponse()
    {
        return $this->client()->chat()->create($this->promt)->toArray();
    }

    /**
     * Curl Request
     *
     * @return string
     */
    public function makeCurlRequest()
    {
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->getUrl(),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => config('openAI.ssl_verify_host'),
        CURLOPT_SSL_VERIFYPEER => config('openAI.ssl_verify_peer'),
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($this->promt),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->aiKey(),
        ),
        ));

        // Make API request
        $response = curl_exec($curl);
        $err = curl_error($curl);
        // Close cURL session
        curl_close($curl);
        $response = !empty($response) ? $response : $err;
        $response =  json_decode($response, true);
        return $response;
    }


    /**
     * Store Images
     * @param mixed $data
     *
     * @return array
     */
    public function save($chatInfo)
    {
        try {

            DB::beginTransaction();
            if (!empty(request('chatId'))) {
                $converstaionId = request('chatId');
            } else {
                $newConversation = new ChatConversation();
                $newConversation->title = request('promt');
                $newConversation->user_id = auth('api')->user()->id;
                $newConversation->save();
                $converstaionId = $newConversation->id;
            }
            // User Message
            $chat = new Chat();
            $chat->chat_conversation_id = $converstaionId;
            $chat->user_id = auth('api')->user()->id;
            $chat->user_message = request('promt');
            $chat->tokens = $chatInfo['usage']['total_tokens'];
            $chat->words = str_word_count($chatInfo['choices'][0]['message']['content']);
            $chat->words = subscription('tokenToWord', $chatInfo['usage']['total_tokens']);
            $chat->characters = strlen($chatInfo['choices'][0]['message']['content']);
            $chat->save();

            // Bot Reply
            $botChat = new Chat();
            $botChat->chat_conversation_id = $converstaionId;
            $botChat->bot_id = $this->assistant()->id;
            $botChat->bot_message = $chatInfo['choices'][0]['message']['content'];
            $botChat->save();
            DB::commit();

        } catch(Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return ['apiResponse' => $chatInfo, 'newChatId' => request('chatId'), 'id' => $converstaionId];
    }

    /**
     * Last chat message
     * @return [type]
     */
    public static function getMyContactListWithLastMessage()
    {
        return Chat::with(['chatConversation'])->select('id', 'chat_conversation_id', 'user_message', 'bot_message', 'created_at')->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->groupBy('chat_conversation_id')->get();
    }

    /**
     * Core Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model()
    {
        return Chat::query();
    }

    /**
     * Find chat by id
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function chatById($id)
    {
        return self::model()->whereChatConversationId($id)->get();
    }


    /**
     * Boot Name
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function getBotName()
    {
        return ChatBot::select('id', 'name', 'code')->first();
    }

    /**
     * Delete chat
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $data = ChatConversation::find($id);
        return !empty($data) ? $data->delete() : false;
    }

    /**
     * Update chat title
     *
     * @param array $data
     * @return bool
     */
    public function update($data)
    {
        if ($chat = ChatConversation::where('id', $data['chatId'])->first()) {
            $chat->title = $data['name'];
            return $chat->save();
        }
        return false;
    }

 }
