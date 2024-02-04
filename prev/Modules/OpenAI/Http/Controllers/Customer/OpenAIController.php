<?php

/**
 * @package OpenAIController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @contributor Soumik Datta <[soumik.techvill@gmail.com]>
 * @created 06-03-2023
 */

namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\ContentService;

class OpenAIController extends Controller
{
    /**
     * Content Service
     *
     * @var object
     */
    protected $contentService;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function templates()
    {

        $data['useCaseSearchUrl'] = route('user.use_case.search');
        $data['userUseCaseFavorites'] = auth()->user()->use_case_favorites;
        $data['useCases'] = $this->contentService->useCases($data['userUseCaseFavorites']);
        $data['useCaseCategories'] = $this->contentService->useCaseCategories();

        return view('openai::blades.templates', $data);
    }

    /**
     * list of all docs
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function documents()
    {
        $service = $this->contentService;
        $data['contents'] = $service->getAll()->paginate(preference('row_per_page'));
        $data['bookmarks'] = auth()->user()->document_bookmarks_openai;
        return view('openai::blades.documents', $data);
    }

    /**
     * list of all favourite docs
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function favouriteDocuments()
    {
        $service = $this->contentService;
        $data['contents'] = $service->getAllFavourite()->paginate(preference('row_per_page'));
        $data['bookmarks'] = auth()->user()->document_bookmarks_openai;

        return view('openai::blades.favourite_documents', $data);
    }

    /**
     * @param mixed $slug
     * @param ContentService $contentService
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function template($slug)
    {
        $service = $this->contentService;
        $data['useCases'] = $service->useCases();
        $data['useCase'] = $service->useCasebySlug($slug);
        $data['options'] = $service->getOption($data['useCase']->id);
        $data['slug'] = $slug;
        $data['promtUrl'] = 'api/V1/user/openai/ask';
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        $data['meta'] = $this->contentService->getMeta('document');

        $data['userSubscription'] = subscription('getUserSubscription');
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);

        return view('openai::blades.document', $data);
    }

    /**
     * Image Template
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function imageTemplate()
    {
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        $data['promtUrl'] = 'api/V1/user/openai/image';
        $data['meta'] = $this->contentService->getMeta('image_maker');

        $data['userSubscription'] = subscription('getUserSubscription');
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);

        return view('openai::blades.image_edit', $data);
    }

    /**
     * Code Template
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function codeTemplate()
    {
        $data['promtRoute'] = 'api/user/openai/image';
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        $data['promtUrl'] = 'api/V1/user/openai/code';
        $data['meta'] = $this->contentService->getMeta('code_writer');

        $data['userSubscription'] = subscription('getUserSubscription');
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);

        return view('openai::blades.code', $data);
    }

     /**
     * Content edit
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function editContent($slug)
    {
        $service = $this->contentService;
        $data['useCases'] = $service->useCases();
        $data['useCase'] = $service->contentBySlug($slug);
        $data['options'] = $service->getOption($data['useCase']->use_case_id);
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        return view('openai::blades.documents-edit', $data);
    }

    /**
     * Update Content
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
    */
    public function updateContent(Request $request)
    {
        return $this->contentService->updateContent($request->contentSlug, $request->content);
    }

    /**
     * Form field by use case
     * @param mixed $slug
     *
     * @return [type]
     */
    public function getFormFiledByUsecase($slug)
    {
        $service = $this->contentService;
        $data['useCase'] = $service->useCasebySlug($slug);
        $data['options'] = $service->getOption($data['useCase']->id);
        return view('openai::blades.form_fields', $data);
    }

     /**
     * Get individual content
     * @param Request $request
     *
     * @return [type]
     */
    public function getContent(Request $request)
    {
        return view('openai::blades.partial-history', $this->contentService->getContent($request->contentId));

    }

    /**
     * delete content
     * @param Request $request
     *
     * @return [type]
     */
    public function deleteContent(Request $request)
    {
        return $this->contentService->delete($request->contentId);
    }

}


