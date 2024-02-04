<?php
/**
 * @package ImageController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 06-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\ImageService;

class ImageController extends Controller
{

    /**
     * Image Service
     *
     * @var object
     */
    protected $imageService;

    /**
     * Constructor
     *
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Store Image via service
     *
     * @param Request $request
     * @return [type]
     */
    public function saveImage($imageUrls)
    {
        return $this->imageService->save($imageUrls);
    }

    /**
     * Image list
     * @return [type]
     */
    public function list()
    {
        $data['images'] = $this->imageService->getAll()->paginate(preference('row_per_page'));
        return view('openai::blades.images.image_list', $data);
    }

    /**
     * Delete image
     * @param Request $request
     *
     * @return [type]
     */
    public function deleteImage(Request $request)
    {
        $response = ['status' => 'error', 'message' => __('The data you are looking for is not found')];

        if ($this->imageService->delete($request->id)) {
            $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Image')])];
        }
        return response()->json($response);
    }

    /**
     * View image
     * @param mixed $slug
     *
     * @return [type]
     */
    public function view($slug)
    {
        $data['images'] = $this->imageService->imageBySlug($slug);
        return view('openai::blades.imageView', $data);
    }
}


