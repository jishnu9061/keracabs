<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/11
 * Time: 12:21:59
 * Description: 'WebBlogController.php'
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Blog;

use Illuminate\Http\Request;

class WebBlogController extends Controller
{
    /**
     * BlogPage
     *
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('web.blog');
        $blogs = Blog::select('id', 'title', 'image', 'description', 'created_at')->limit(8)->get();
        $recentBlogs = Blog::select('id', 'title', 'image', 'description', 'created_at')->latest()->limit(4)->get();
        $para = ['blogs' => $blogs, 'recentBlogs' => $recentBlogs];
        $title = 'Blog';
        return $this->renderView($path, $para, $title);
    }

    /**
     * BlogDetailPage
     *
     * @param Request $request
     * @param Blog $blog
     *
     * @return [type]
     */
    public function blogDetailPage(Request $request, Blog $blog)
    {
        $path = $this->getView('web.blog-detail');
        $recentPosts = Blog::select('id', 'title', 'image', 'description', 'created_at')->latest()->limit(4)->get();
        $para = ['recentPosts' => $recentPosts, 'blog' => $blog];
        $title = 'Blog Detail';
        return $this->renderView($path, $para, $title);
    }
}
