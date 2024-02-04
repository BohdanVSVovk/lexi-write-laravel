<?php

/**
 * @package ImageService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 22-03-2023
 */

namespace Modules\OpenAI\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Modules\OpenAI\Entities\{
    Image,
    ContentTypeMeta
};
use App\Models\{
    User
};

 class ImageService
 {
    protected $formData;
    public $imageName;
    public $images;
    protected $promt;
    protected $imageNames;
    protected $class;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($formData = null, $imageName = null, $images = null, $promt = null, $imageNames = null, $class = null)
    {
        $this->formData = $formData;
        $this->imageName = $imageName;
        $this->images = $images;
        $this->promt = $promt;
        $this->imageNames = $imageNames;
        $this->class = $class;
    }

    /**
     * Define storage path
     * This path will come dynamically from admin settings
     * @return [type]
     */
    public function storagePath()
    {
        return URL::to('/'). DIRECTORY_SEPARATOR . $this->uploadPath();
    }


    /**
     * Image create
     * @param mixed $data
     *
     * @return [type]
     */
    public function createImage($data)
    {
        $this->formData = $data;
        return $this->validate();
    }

    /**
     * Go the the specific class
     *
     * @return array
     */
    public function imageClass()
    {
        $usedApi = json_decode(ContentTypeMeta::where(['key' => 'imageCreateFrom'])->value('value'));
        $class = 'Modules\OpenAI\Libraries'. "\\" . $usedApi[0];
        if (class_exists($class, true)) {
            $this->class = new $class($this);
            return $this->preparePromt();
        } else {
            return [
                'status' => 'error',
            ];
        }
    }

    /**
     * prepare promt
     * @return [type]
     */
    public function preparePromt()
    {
        return $this->class->promt($this->formData);
    }

     /**
     * validate form data
     * @return array
     */
    public function validate()
    {
        app('Modules\OpenAI\Http\Requests\ImageStoreRequest')->safe();
        return $this->imageClass();
    }

     
    /**
     * Create upload path
     * @return [type]
     */
    protected function uploadPath()
	{
		return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads','aiImages']));
	}

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function upload($url)
    {
        $filename = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(" ", "_",  $this->createName(request('promt'))));
        $filename = md5(uniqid()) . "." . "jpg";
        $this->imageName = $filename;
        return file_put_contents($this->uploadPath() . DIRECTORY_SEPARATOR . $filename, file_get_contents($url));
    }

    /**
     * Image store in DB
     * @param mixed $images
     *
     * @return [type]
     */
    public function storeData($image, $name)
    {
        Image::insert($image);
        return $name;
    }

    /**
     * Image Name Creation
     *
     * @param null $name
     * @return string
     */
    public function createName($name = null)
    {
        return !empty($name) ? substr($name, 0, 100) : Str::random(100);
    }


     /**
      * Slug Creator

      * @param string $name
      * @return string
      */
    public function createSlug($name)
    {
        if (!empty($name)) {

            $slug = cleanedUrl($name);

            if(Image::whereSlug($slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            return $slug;
        }
    }

    /**
     * Get All images
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        $result = Image::query();
        $userRole = auth()->user()->roles()->first();
        if ($userRole->type == 'user') {
            $result = $result->where('user_id', auth()->user()->id);
        }
        return $result->latest();
    }

    /**
     * Core Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model()
    {
        return Image::with(['user:id,name']);
    }

     /**
     * Details of image
     * @return string
     */
    public function details($id)
    {
        $details = $this->model()->where('id', $id)->first();
        return !empty($details) ? $details : false;
    }

    /**
     * Delete image
     *
     * @param mixed $id
     * @return bool
     */
    public function delete($id)
    {
        $image = $this->model()->where('id', $id)->first();
        $isDeleted = empty($image) ? false : $image->delete();
        if ($isDeleted) {
            return $this->unlinkFile($image->original_name);
        }

        return $isDeleted;
    }

    /**
     * Unlink image
     * @param mixed $name
     *
     * @return [type]
     */
    protected function unlinkFile($name)
    {
        if (file_exists($this->imagePath($name))) {
            unlink($this->imagePath($name));
          }
        return true;
    }

    /**
     * Image path
     * @param mixed $name
     *
     * @return [type]
     */
    public static function imagePath($name)
    {
        return public_path('uploads'). DIRECTORY_SEPARATOR . 'aiImages'. DIRECTORY_SEPARATOR . $name;
    }

    /**
     * image url through id
     * @param mixed $id
     *
     * @return [type]
     */
    public static function imageUrl($id)
    {
        $image = self::model()->where('id', $id)->first();
        return !empty($image) ? self::imagePath($image->original_name) : '';
    }

    /**
     * image view through id
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function view($id)
    {
        return $this->model()->where('id', $id)->firstOrFail();
    }

    /**
     * Image By Slug
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function imageBySlug($slug)
    {
        return $this->model()->whereSlug($slug)->firstOrFail();
    }

      /**
     * Users Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function users()
    {
        return User::get();
    }

    /**
     * Size of image
     *
     * @return string
     */
    public function sizes()
    {
        return config('openAI.size');
    }

    /**
     * get hight width data
     *
     * @param string $string
     * @return array
     */
    public function explodedData($string)
    {
       return explode("x", $string);
    }


 }
