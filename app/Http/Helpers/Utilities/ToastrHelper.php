<?php

namespace App\Http\Helpers\Utilities;

use Session;

class ToastrHelper
{

    /**
     * Success message function
     *
     * @param string $message
     */
    public static function success($message)
    {
        $data = [
            'message' => $message,
            'level' => __FUNCTION__,
        ];
        Session::put('appNotificaion', $data);
    }

    /**
     * Error message function
     *
     * @param string $message
     */
    public static function error($message)
    {
        $data = [
            'message' => $message,
            'level' => __FUNCTION__,
        ];
        Session::put('appNotificaion', $data);
    }

    /**
     * Warning message function
     *
     * @param type $message
     */
    public static function warning($message)
    {
        $data = [
            'message' => $message,
            'level' => __FUNCTION__,
        ];
        Session::put('appNotificaion', $data);
    }

    /**
     * Notify message function
     *
     * @param type $message
     */
    public static function notify($message)
    {
        $data = [
            'message' => $message,
            'level' => __FUNCTION__,
        ];
        Session::put('appNotificaion', $data);
    }

}
