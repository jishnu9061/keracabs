<?php

namespace App\Http\Controllers;

use App\Services\Utility\BreadcrumbService;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $_route;
    protected $_directRoute;
    protected $_view = 'pages.';
    protected $_directView;

    private $_breadcrumb;

    public function __construct(Request $request)
    {
        // $this->_breadcrumb = new BreadcrumbService($request);
        // $this->_breadcrumb->compose();

        $this->middleware(function ($request, $next) {
//            if (Auth::check()) {
//                $systemNotifications = new SystemNotifications($request, Auth::id());
//                $systemNotifications->compose();
//            }
            return $next($request);
        });
    }

    /**
     * Rename Breadcrumb from view
     */
    protected function renameBreadcrumb(Request $request, string $renamefrom, string $renameTo)
    {
        $breadcrumbSegments = collect($request->segments())->mapWithKeys(function ($segment, $key)use($request, $renamefrom, $renameTo) {
            if ($segment == $renamefrom) {
                $segment = $renameTo;
            }
            return [
                $segment => implode('/', array_slice($request->segments(), 0, $key + 1))
            ];
        })->toArray();
        $this->_breadcrumb->compose($breadcrumbSegments);
    }

    /**
     * Exclude Breadcrumb from view
     */
    protected function excludeBreadcrumb()
    {
        $this->_breadcrumb->compose([], true);
    }

    /**
     * Add Home URl (add this before adding additional breadcrumb)
     *
     * @param string $url
     */
    protected function addHomeBreadcrumb(string  $url = '')
    {
        $this->_breadcrumb->addHome(['Home' => $url]);
    }

    /**
     * Add Additional Breadcrumb
     *
     * @param array $urls
     */
    protected function addBreadcrumb(array $urls = [])
    {
        $this->_breadcrumb->compose($urls);
    }

    protected function addBaseRoute($path)
    {
        $this->_route .= $path;
    }

    protected function getRoute($path)
    {
        return ('' != $path)? $this->_route . '.' . $path : $this->_route ;
    }

    protected function getDirectRoute($path)
    {
        return ('' != $path)? $this->_directRoute . '.' . $path : $this->_directRoute ;
    }

    protected function addBaseView($path)
    {
        $this->_view .= $path;
    }

    protected function getView($path)
    {
        return ('' != $path)? $this->_view . '.' . $path : $this->_view ;
    }

    protected function getDirectView($path)
    {
        return ('' != $path)? $this->_directView . '.' . $path : $this->_directView ;
    }

    /**
     * Render view
     * @param $view
     * @param $data
     * @param $title
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderView($view, $data, $title)
    {
        $data['title'] = $title;
        return view($view, $data);
    }

}
