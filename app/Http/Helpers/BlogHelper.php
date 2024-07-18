<?php

namespace App\Http\Helpers;

use App\Http\Constants\FileDestinations;

use App\Http\Helpers\Core\FileManager;

class BlogHelper
{
    /**
     * get image path of course
     *
     * @param $imageName
     * @return string
     */

    public static function getBlogImagePath($imageName)
    {
        $file = asset('images/blog.jpg');
        if (null != $imageName) {
            if (FileManager::checkFileExist($imageName, FileDestinations::BLOG_IMAGE)) {
                $file = FileManager::getFileUrl($imageName, FileDestinations::BLOG_IMAGE);
            }
        }
        return $file;
    }

    /**
     * @param mixed $imageName
     *
     * @return [type]
     */
    public static function getBannerImagePath($imageName)
    {
        $file = asset('images/blog.jpg');
        if (null != $imageName) {
            if (FileManager::checkFileExist($imageName, FileDestinations::BANNER_IMAGE)) {
                $file = FileManager::getFileUrl($imageName, FileDestinations::BANNER_IMAGE);
            }
        }
        return $file;
    }
}
