<?php

/**
 * @package ContentService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 07-03-2023
 */

namespace Modules\OpenAI\Services;

use App\Models\{
    Language,
    User
};
use Illuminate\Support\Str;
use Modules\OpenAI\Entities\{
    Content,
    ContentTypeMeta,
    ContentType,
    UseCase,
    Option,
    UseCaseCategory
};
use Modules\OpenAI\Traits\MetaTrait;

 class ContentService
 {
    use MetaTrait;
    protected $formData;
    protected $preparedData;
    protected $response;
    protected $failedResponse;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($formData = null, $preparedData = null, $response = null, $failedResponse = null)
    {
        $this->formData = $formData;
        $this->preparedData = $preparedData;
        $this->response = $response;
        $this->failedResponse = [
            'status' => 'failed',
        ];
    }

    /**
     * Prapare data for insertation
     *
     * @param mixed $data
     * @param mixed $useCaseId
     * @param mixed $promt
     * @param mixed $response
     *
     * @return [type]
     */
    public function prepareData($data, $useCaseId, $promt, $response)
    {
        $contents = '';
        $promtText = array_values(json_decode($data['questions'], true));
        $responses = count($response['choices']);
        $model = preference('ai_model');
        for($i = 0; $i < $responses; $i++) {
            $contents .= in_array($model, $this->chatModel()) ? $response['choices'][$i]['message']['content'] : $response['choices'][$i]['text'];

            if ($responses > 1 && $responses-1 > $i) {
                $contents .= "<br>";
                $contents .= "<hr>";
            }

        }

        $preparedData =  [
            'user_id' => auth('api')->user()->id,
            'use_case_id' => $useCaseId,
            'title' => implode(',', $promtText),
            'slug' => $this->createSlug(implode(',', $promtText)),
            'promt' => $promt,
            'content' => $contents,
            'tokens' => $response['usage']['total_tokens'],
            'words' => subscription('tokenToWord', $response['usage']['total_tokens']),
            'characters' => strlen($contents),
            'model' => preference('ai_model'),
            'language' => $data['language'],
            'tone' => $data['tone'],
            'creativity_label' => $data['temperature'],
        ];
        $this->response = $response;
        // Added total used word in array
        $this->response->words = str_word_count($contents);

        $this->preparedData = $preparedData;
        if (request('contentSlug') && !empty(request('previousContent'))) {
            return $this->update();
         }
        // Prepare data for validation
        request()->merge($preparedData);
        return $this->validate();
    }

    /**
     * validate form data
     * @return [type]
     */
    public function validate()
    {
        app('Modules\OpenAI\Http\Requests\ContentStoreRequest')->safe();
        return $this->store();
    }

     /**
     * Store Content
     *
     * @return [type]
     */
    public function store()
    {
       return Content::insert($this->preparedData) ? $this->response : $this->failedResponse;
    }

    /**
     * Update Content
     *
     * @return [type]
     */
    protected function update()
    {
        $response = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('Content')])];
        $content = Content::where('slug', request('contentSlug'))->first();

        if ($content) {
            $content->content = $content->content . '<br /><br />' . nl2br($this->response[0]['text']);
            $content->words = $content->words + str_word_count($this->response[0]['text']);
            $content->characters = $content->characters + strlen($this->response[0]['text']);
            $content->save();

            return response()->json(['status' => 'success', 'message' => __('The :x has been successfully saved.', ['x' => __('Content')])]);
        }

        return response()->json($response);
    }

     /**
     * Create slug
     *
     * @param mixed $name
     * @return string
     */
    protected function createSlug($name)
    {
        if (!empty($name)) {

            $slug = cleanedUrl($name);

            if (Content::whereSlug($slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            return $slug;
        }
    }

    /**
     * Get All Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        $result = $this->model()->with('useCase');

        $userRole = auth()->user()->roles()->first();
        if ($userRole->type == 'user') {
            $result = $result->where('user_id', auth()->user()->id);
        }
        return $result->whereNull('parent_id')->latest();
    }

    /**
     * Get All Favorite
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFavourite()
    {
        $bookmarks = auth()->user()->document_bookmarks_openai;

        $result = $this->model()->with('useCase')->whereIn('id', $bookmarks);

        $userRole = auth()->user()->roles()->first();
        if ($userRole->type == 'user') {
            $result = $result->where('user_id', auth()->user()->id);
        }
        return $result->whereNull('parent_id')->latest();
    }

    /**
     * Content By Slug
     *
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function contentBySlug($slug)
    {
        return Content::with(['useCase', 'User'])->whereSlug($slug)->firstOrFail();
    }

    /**
     * Use Cases
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function useCases($data = null)
    {
        $useCases = UseCase::where('status', 'Active')->get();

        if ($data != null) {

            $favUseCases = $useCases->whereIn('id', $data);
            $exceptFavUseCases = $useCases->whereNotIn('id', $data);

            $useCases =  $favUseCases->merge($exceptFavUseCases);

        }

        return $useCases;

    }

    /**
     * Language Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function languages()
    {
        return Language::where(['status' => 'Active'])->get();
    }

    /**
     * Users Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function users()
    {
        return User::where(['status' => 'Active'])->get();
    }

    /**
     * Content Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model()
    {
        return Content::with(['useCase:slug,creator_id', 'user:id,name']);
    }

    /**
     * Get Content
     *
     * @param mixed $contentId
     * @return array
     */
    public function getContent($contentId)
    {
        $data['partialContent'] = Content::with(['useCase', 'option'])->where('id', $contentId)->firstOrFail();
        $data['questions'] = $this->getQuestions($data['partialContent']->title);
        $data['wrodCount'] = str_word_count($data['partialContent']->content);
        return $data;
    }

    /**
     * Get Question
     *
     * @param string $string
     * @return array
     */
    protected function getQuestions($string)
    {
        return explode("," , $string);
    }
    /**
     * get use case by slug
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function useCasebySlug($name)
    {
        return UseCase::where('status', 'active')->whereSlug($name)->firstOrFail();
    }

    /**
     * get use case by slug
     *
     * @param int $useCaseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOption($useCaseId)
    {
        return Option::whereUseCaseId($useCaseId)->get();
    }

    /**
     * @param mixed $id
     *
     * @return [type]
     */
    public static function delete($id)
    {
        if ($content = Content::find($id)) {
            try {
                $content->delete();
                $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Content')])];

            } catch (\Exception $e) {
                $response = ['status' => 'fail', 'message' => $e->getMessage()];
            }
            return response()->json($response);

        }
        $response = ['status' => 'fail', 'message' => __('The data you are looking for is not found')];
        return response()->json($response);
    }

    /**
     * Update Content
     *
     * @param string $contentSlug
     * @param string $contents
     * @return \Illuminate\Http\Response
     */
    public function updateContent($contentSlug, $contents)
    {
        $response = ['status' => 'error', 'message' => __('The :x does not exist.', ['x' => __('Content')])];
        $content = Content::where('slug', $contentSlug)->first();

        if ($content) {
            $content->content = $contents;
            $content->save();
            $response = ['status' => 'success', 'message' => __('The Content Updated successfully.')];
        }

        return response()->json($response);
    }

    /**
     * Create Version
     *
     * @param object $content
     * @param array $data
     * @return bool
     */
    public function createVersion($content, $data)
    {
        $content->content = str_ireplace('<br>', "\n", $data['content']);
        return $content->save();
    }

    /**
     * Content Update
     *
     * @param array $data
     * @return bool
     */
    public function contentUpdate($data)
    {
        $content = Content::where('id', $data['id'])->firstOrFail();
        $content->content = str_ireplace('<br>', "\n", $data['content']);
        $content->use_case_id = $data['use_case_id'];
        return $content->save();
    }

    /**
     * Use Case Categories
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function useCaseCategories()
    {
       return UseCaseCategory::with([
            'useCases' => function ($query) {
                $query->select('id', 'name', 'description', 'slug')
                    ->where('status', 'Active');
            }
        ])->get();
    }

    /**
     * Contents Features
     *
     * @return array
     */
    public static function features(): array
    {
        /**
         * Type will be bool, number, string
         * title_position will be before, after
         * When added new key and value it will need to add in blade file
         */
        return [

            'document' => [

                'tone' => [
                    'Casual' => 'Casual',
                    'Funny' => 'Funny',
                    'Bold' => 'Bold',
                    'Femenine' => 'Femenine',
                ],

                'variant' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],

                'temperature' => [
                    'Optimal' => 'Optimal',
                    'Low' => 'Low',
                    'Medium' => 'Medium',
                    'High' => 'High',
                ]

            ],

            'image_maker' => [

                'variant' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],

                'resulation' => [
                    '256x256' => '256x256',
                    '512x512' =>  '512x512',
                    '1024x1024' =>  '1024x1024',
                ],

                'artStyle' => [
                    'Normal' => 'Normal',
                    'Cartoon art' => 'Cartoon art',
                    '3D Render' => '3D Render',
                    'Pixel art' => 'Pixel art',
                    'Isometric' => 'Isometric',
                    'Vendor art' => 'Vendor art',
                    'Line art' => 'Line art',
                    'Watercolor art' => 'Watercolor art',
                    'Anime art' => 'Anime art'
                ],

                'lightingStyle' => [
                    'Normal' => 'Normal',
                    'Studio' => 'Studio',
                    'Warm' => 'Warm',
                    'Cold' => 'Cold',
                    'Ambient' => 'Ambient',
                    'Neon' => 'Neon',
                    'Foggy' => 'Foggy'
                ],
                'imageCreateFrom' => [
                    'Openai' => 'OpenAI',
                    'Stablediffusion' => 'Stable Diffusion',
                ]

            ],

            'code_writer' => [

                'language' => [
                    'PHP' => 'PHP',
                    'Java' =>  'Java',
                    'Rubby' =>  'Rubby',
                    'Python' => 'Python',
                    'C#' =>'C#' ,
                    'Go' => 'Go',
                    'Kotlin' => 'Kotlin',
                    'HTML' => 'HTML',
                    'Javascript' => 'Javascript',
                    'TypeScript' => 'TypeScript',
                    'SQL' => 'SQL',
                    'NoSQL' => 'NoSQL'
                ],

                'codeLabel' => [
                    'Noob' => 'Noob',
                    'Moderate' => 'Moderate',
                    'High' => 'High',
                ]
            ],

        ];
    }

    /**
     * Store meta data
     *
     * @param array $data
     * @return array
     */
    public function storeMeta($metaData)
    {
        $properties = [];

        if (is_array($metaData) || is_object($metaData)) {
            foreach ($metaData as $key => $data) {
                $id = $this->contentType($key);

                foreach ($data as $k => $value) {
                    $properties[] = [
                        'content_type_id' => $id,
                        'name' => $key,
                        'key' => $k,
                        'value' => json_encode($value),
                    ];
                }
            }
            ContentTypeMeta::upsert($properties, ['content_type_id','key']);
            $response = ['status' => 'success', 'message' => __('The :x has been successfully saved.', ['x' => __('AI Preference Settings')])];
        } else {
            $response = ['status' => 'fail', 'message' => __('At Least one Item has to be selected.')];
        }

        return $response;
    }


    /**
     * get all preferences meta data
     *
     * @param array $data
     * @return array
     */
    public function getAllMeta()
    {
        $data = [];

        foreach ( ContentType::get() as $meta ) {
            if ( array_key_exists($meta->slug, $this->features()) ){
                $data[$meta->slug]= ContentTypeMeta::where('content_type_id', $meta->id)->get();
            }
        }

        return $data;
    }

    /**
     * get meta data
     *
     * @param array $data
     * @return array
     */
    public function getMeta($slug = null)
    {
        return ContentType::getData($slug);
    }

    /**
     * Process input value
     * @param mixed $value
     * @return mixed
     */
    private function contentType($value)
    {
        return ContentType::where('slug',$value)->value('id');
    }

     /**
     * get use cases by slug
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function useCasesbySlug($name)
    {
        $parent = self::contentBySlug($name);
        $child = Content::with(['useCase'])->where('id', $parent->id)->orWhere('parent_id', $parent->id)->get();
        return !empty($child) ? $child : $parent;
    }

    /**
     * define chat model
     * @return array
     */
    public function chatModel()
    {
        return [
            'gpt-3.5-turbo',
            'gpt-4',
            'gpt-3.5-turbo-16k'
        ];
    }

 }
