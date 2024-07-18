<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/13
 * Time: 13:49:19
 * Description: BlogController.php
 */

namespace App\Http\Controllers;

use App\Http\Constants\FileDestinations;

use App\Http\Helpers\Utilities\ToastrHelper;
use App\Http\Helpers\Core\FileManager;

use App\Models\Blog;

use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;

use Illuminate\Support\Facades\Response;

class BlogController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('admin.blog.index');
        $blogs = Blog::select('id', 'title', 'image', 'created_at', 'description')->get();
        $para = ['blogs' => $blogs];
        $title = 'Blogs';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @return [type]
     */
    public function create()
    {
        $path = $this->getView('admin.blog.create');
        $para = [];
        $title = 'Create Blog';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param BlogStoreRequest $request
     *
     * @return [type]
     */
    public function store(BlogStoreRequest $request)
    {
        try {
            $blog = Blog::create([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            if ($request->hasFile('image')) {
                $res = FileManager::upload(FileDestinations::BLOG_IMAGE, 'image', FileManager::FILE_TYPE_IMAGE);
                if ($res['status']) {
                    $blog->image = $res['data']['fileName'];
                    $blog->save();
                }
            }
            ToastrHelper::success('Blog added successfully');
            return redirect()->route('blog.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create outlet. ' . $e->getMessage());
        }
    }

    /**
     * @param Blog $blog
     *
     * @return [type]
     */
    public function edit(Blog $blog)
    {
        $path = $this->getView('admin.blog.edit');
        $para = ['blog' => $blog];
        $title = 'Edit Outlet';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param BlogUpdateRequest $request
     * @param Blog $blog
     *
     * @return [type]
     */
    public function update(BlogUpdateRequest $request, Blog $blog)
    {
        $blog->update([
            'title' => $request->title,
            'blog_details' => $request->blog_details,
        ]);
        if ($request->hasFile('image')) {
            $res = FileManager::upload(FileDestinations::BLOG_IMAGE, 'image', FileManager::FILE_TYPE_IMAGE);
            if ($res['status']) {
                $blog->image = $res['data']['fileName'];
                $blog->save();
            }
        }
        ToastrHelper::success('Blog updated successfully');
        return redirect()->route('blog.index');
    }

    /**
     * @param Blog $blog
     *
     * @return [type]
     */
    public function delete(Blog $blog)
    {
        $blog->delete();
        ToastrHelper::success('Blog deleted successfully');
        return Response::json(['success' => 'Blog Deleted Successfully']);
    }
}
