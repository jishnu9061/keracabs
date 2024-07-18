<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/11
 * Time: 12:22:30
 * Description: 'WebContactController.php'
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class WebContactController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('web.contact');
        $para = [];
        $title = 'Contact';
        return $this->renderView($path, $para, $title);
    }
}
