<?php
/**
 * @package ImageController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 06-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\{
    ImageService,
    ContentService
};
use Modules\OpenAI\DataTables\{
    ImageDataTable
};

class ImageController extends Controller
{

    /**
     * Image Service
     *
     * @var object
     */
    protected $imageService;

    /**
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Store Image via service
     * @param Request $request
     *
     * @return [type]
     */
    public function saveImage($imageUrls)
    {
        return $this->imageService->save($imageUrls);
    }

    /**
     * Image list
     *
     * @param ImageDataTable $imageDataTable
     * @return mixed
     */
    public function list(ImageDataTable $imageDataTable, ContentService $contentService)
    {
        $data['sizes'] = $contentService->features()['image_maker']['resulation'];
        $data['users'] = $this->imageService->users();
        return $imageDataTable->render('openai::admin.image.index', $data);
    }

    /**
     * Delete image
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImages(Request $request)
    {
        if ($this->imageService->delete($request->id)) {
            return redirect()->back()->withSuccess(__('The :x has been successfully deleted.', ['x' => __('Image')]));
        }
        return redirect()->back()->withFail(__('Failed to delete image. Please try again.'));
    }

    /**
     * View image
     *
     * @param mixed $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function view($slug)
    {
        $data['images'] = $this->imageService->imageBySlug($slug);
        return view('openai::blades.imageView', $data);
    }
}


