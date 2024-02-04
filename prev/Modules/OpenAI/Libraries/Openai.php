<?php

/**
 * @package OpenAI
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 24-05-2023
 */

namespace Modules\OpenAI\Libraries;

 class Openai
 {
    protected $url = 'https://api.openai.com/v1/images/generations';
    protected $promt;
    protected $imageService;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * prepare promt
     * @return [type]
     */
    public function promt($data)
    {
        $this->promt = [
            "prompt" => 'Please generate images of' . $data['promt']. 'in a ' . $data['lightingStyle'] . 'with the art of ' . $data['artStyle'],
            "n" => (int) $data['variant'],
            "size" => $data['resulation']
        ];

        return $this->response($this->getResponse());
    }

    /**
     * Get Response
     *
     * @return array
     */
    public function getResponse() {
        $client = \OpenAI::client(apiKey('openai'));

        return $client->images()->create($this->promt);
    }

    /**
     * Curl Request
     * @return [type]
     */
    public function response($response)
    {
        if (isset($response['created'])) {
            return $this->save($response);
        } else if(isset($response['error'])) {
            return [
                'response' => $response['error']['message'],
                'status' => 'error',
            ];
        }

    }

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function save($data)
    {
        $totalImages = count($data['data']);

        for ($i = 0; $i < $totalImages; $i++) {
            $this->imageService->upload($data['data'][$i]['url']);
            $slug = $totalImages > 1 ? $this->imageService->createSlug(request('promt') . $i) : $this->imageService->createSlug(request('promt'));
            $name = $this->imageService->createName(request('promt'));
            $images[] = [
                'user_id' => auth('api')->user()->id,
                'name' => $name,
                'original_name' => $this->imageService->imageName,
                'promt' => request('promt'),
                'slug' => $slug,
                'size' => request('resulation'),
                'art_style' => request('artStyle'),
                'lighting_style' => request('lightingStyle'),
                'libraries' => 'Openai',
                'meta' => json_encode($data),
            ];
            $imageNames[] = [
                'url' => $this->imageService->storagePath() . DIRECTORY_SEPARATOR . $this->imageService->imageName,
                'slug_url' => route("user.image.view", ["slug" => $slug]),
                'name' => $name
            ];
        }
       return $this->imageService->storeData($images, $imageNames);
    }

 }
