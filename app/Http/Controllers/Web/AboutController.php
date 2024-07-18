<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/11
 * Time: 12:18:55
 * Description: AboutController.php
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    /**
     * About Page
     *
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('web.about');
        $para = [];
        $title = 'About ';
        return $this->renderView($path, $para, $title);
    }
}
