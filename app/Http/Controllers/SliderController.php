<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/13
 * Time: 13:46:07
 * Description: SliderController.php
 */

namespace App\Http\Controllers;

use App\Http\Constants\FileDestinations;

use App\Models\Banner;

use App\Http\Helpers\Utilities\ToastrHelper;
use App\Http\Helpers\Core\FileManager;

use App\Http\Requests\BannerStoreRequest;
use App\Http\Requests\BannerUpdateRequest;

use Illuminate\Support\Facades\Response;

class SliderController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('admin.slider.index');
        $banners = Banner::select('id', 'image', 'created_at')->get();
        $para = ['banners' => $banners];
        $title = 'banners';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @return [type]
     */
    public function create()
    {
        $path = $this->getView('admin.slider.create');
        $para = [];
        $title = 'Create Slider';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param BannerStoreRequest $request
     *
     * @return [type]
     */
    public function store(BannerStoreRequest $request)
    {
        try {
            $banner = new Banner();
            if ($request->hasFile('image')) {
                $res = FileManager::upload(FileDestinations::BANNER_IMAGE, 'image', FileManager::FILE_TYPE_IMAGE);
                if ($res['status']) {
                    $banner->image = $res['data']['fileName'];
                    $banner->save();
                }
            }
            ToastrHelper::success('Slider uploaded successfully');
            return redirect()->route('slider.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload image. ' . $e->getMessage());
        }
    }


    /**
     * @param Banner $banner
     *
     * @return [type]
     */
    public function edit(Banner $banner)
    {
        $path = $this->getView('admin.slider.edit');
        $para = ['banner' => $banner];
        $title = 'Edit Slider';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param BannerUpdateRequest $request
     * @param Banner $banner
     *
     * @return [type]
     */
    public function update(BannerUpdateRequest $request, Banner $banner)
    {
        if ($request->hasFile('image')) {
            $res = FileManager::upload(FileDestinations::BANNER_IMAGE, 'image', FileManager::FILE_TYPE_IMAGE);
            if ($res['status']) {
                $banner->image = $res['data']['fileName'];
                $banner->save();
            }
        }
        ToastrHelper::success('Slider updated successfully');
        return redirect()->route('slider.index');
    }

    /**
     * @param Banner $banner
     *
     * @return [type]
     */
    public function delete(Banner $banner)
    {
        $banner->delete();
        ToastrHelper::success('Slider deleted successfully');
        return Response::json(['success' => 'Banner Deleted Successfully']);
    }
}
