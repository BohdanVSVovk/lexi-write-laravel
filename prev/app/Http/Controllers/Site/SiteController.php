<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Cache};
use Modules\CMS\Http\Models\{Page};

class SiteController extends Controller
{
    /**
     * Change Language function
     *
     * @param Request $request
     * @return bool
     */
    public function switchLanguage(Request $request): bool
    {
        if ($request->lang) {
            if (!empty(Auth::user()->id) && isset(Auth::guard('user')->user()->id)) {
                Cache::put(config('cache.prefix') . '-user-language-' . Auth::guard('user')->user()->id, $request->lang, 5 * 365 * 86400);
                return true;
            } else {
                Cache::put(config('cache.prefix') . '-guest-language-' . md5(request()->server('HTTP_USER_AGENT') . getIpAddress()), $request->lang, 5 * 365 * 86400);
                return true;
            }
        }

        return false;
    }

    /**
     * Pages
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function page($slug)
    {
        $data['page'] = Page::getAll()->where('slug', $slug)->where('status', 'Active')->first();

        if (isset($data['page'])) {
            return view('site.pages.page', $data);
        }

        abort(404);
    }
}
