<?php

/**
 * @package Stable Diffusion
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 24-05-2023
 */

namespace Modules\OpenAI\Libraries;

 class Stablediffusion
 {
    /**
     * URL
     *
     * @var string
     */
    protected $url = 'https://stablediffusionapi.com/api/v3/text2img';

    /**
     * Promt
     *
     * @var string
     */
    protected $promt;

    /**
     * Image to Image Prompt
     *
     * @var string
     */
    protected $imageToImagePromt;

    /**
     * Image service
     *
     * @var object
     */
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
     * Image to Image Conversion
     *
     * @param array $data
     * @return void
     */
    public function imageToImage($data)
    {
        $imgHeightWidth = $this->imageService->explodedData(request('resulation'));
        $this->promt = ([
            "key" => apiKey('stablediffusion'),
            "prompt" => $data['promt'],
            "negative_prompt" => null,
            "init_image" => $data['file'],
            "width" => $imgHeightWidth[1],
            "height" => $imgHeightWidth[0],
            "samples" => $data['variant'],
            "num_inference_steps" => "30",
            "safety_checker" => "no",
            "enhance_prompt" => "yes",
            "guidance_scale" => 7.5,
            "strength" => 0.7,
            "multi_lingual" => "yes",
            "panorama" => "yes",
            "seed" => null,
            "webhook" => null,
            "track_id" => null
        ]);
    }


    /**
     * Text to Image
     * @param mixed $data
     *
     * @return [type]
     */
    public function generalPromt($data)
    {
        $imgHeightWidth = $this->imageService->explodedData(request('resulation'));
        $this->promt = ([
            "key" => apiKey('stablediffusion'),
            "prompt" => 'Please generate image of ' . $data['promt'] . ' in a ' . $data['lightingStyle'] . ' mode and the art style is ' . $data['artStyle'],
            "negative_prompt" => "((out of frame)), ((extra fingers)), mutated hands, ((poorly drawn hands)), ((poorly drawn face)), (((mutation))), (((deformed))), (((tiling))), ((naked)), ((tile)), ((fleshpile)), ((ugly)), (((abstract))), blurry, ((bad anatomy)), ((bad proportions)), ((extra limbs)), cloned face, (((skinny))), glitchy, ((extra breasts)), ((double torso)), ((extra arms)), ((extra hands)), ((mangled fingers)), ((missing breasts)), (missing lips), ((ugly face)), ((fat)), ((extra legs)), anime",
            "width" => $imgHeightWidth[1],
            "height" => $imgHeightWidth[0],
            "samples" => $data['variant'],
            "num_inference_steps" => "30",
            "safety_checker" => "no",
            "enhance_prompt" => "yes",
            "seed" => null,
            "guidance_scale" => 7.5,
            "multi_lingual" => "yes",
            "panorama" => "yes",
            "self_attention" => "yes",
            "webhook" => null,
            "track_id" => null
        ]);
    }

    /**
     * prepare promt
     *
     * @param array $data
     * @return [type]
     */
    public function promt($data)
    {
        !empty($data['file']) ? $this->imageToImage($data) : $this->generalPromt($data);
        return $this->makeCurlRequest([$this->url, $this->promt]);
    }

    /**
     * Curl Request
     * @return [type]
     */
    public function response($response)
    {
        if (isset($response['status']) && $response['status'] == 'success') {
            return $this->save($response);
        } else {
            $data = [
                'response' => $response['message'],
                'status' => $response['status'],
            ];
            return $data;
        }

    }

    /**
     * Curl Request
     *
     * @return [type]
     */
    public function makeCurlRequest($curlOptions = [])
    {
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curlOptions[0],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => config('openAI.ssl_verify_host'),
            CURLOPT_SSL_VERIFYPEER => config('openAI.ssl_verify_peer'),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($curlOptions[1]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . (isset($curlOptions[2]) ? $curlOptions[2] : '')
            ),
        ));

        // Make API request
        $response = curl_exec($curl);

        // Close cURL session
        curl_close($curl);
        $response = json_decode($response, true);
        return $this->response($response);
    }

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function save($data)
    {
        $totalImages = count($data['output']);

        for ($i = 0; $i < $totalImages; $i++) {
            $this->imageService->upload($data['output'][$i]);
            $slug = $totalImages > 0 ? $this->imageService->createSlug(request('promt') . $i) : $this->imageService->createSlug(request('promt'));
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
                'libraries' => 'Stablediffusion',
                'meta' => json_encode($data['meta']),
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
