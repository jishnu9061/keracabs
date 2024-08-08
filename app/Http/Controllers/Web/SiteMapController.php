<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SiteMapController extends Controller
{
    public function index()
    {
        $path = $this->getView('web.sitemap');
        $para = [];
        $title = 'Site Map';
        return $this->renderView($path, $para, $title);
    }
}
