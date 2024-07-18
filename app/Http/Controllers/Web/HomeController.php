<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/13
 * Time: 13:48:40
 * Description: HomeController.php
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;

class HomeController extends Controller
{
    /**
     * HomePage
     *
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('web.home');
        $blogs = Blog::select('id', 'title', 'image', 'description', 'created_at')->latest()->limit(3)->get();
        $slides = Banner::select('id','image')->get();
        $para = ['blogs' => $blogs,'slides'=>$slides];
        $title = 'Home';
        return $this->renderView($path, $para, $title);
    }
}
