<?php


namespace App\Http\Helpers\Utilities;

use App\Http\Constants\AuthConstants;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class AuthMessages
{

    public static function getLoginStatus($status = AuthConstants::LOGIN_SUCCESS)
    {
        $format = '<span class="badge rounded-pill %s">%s</span>';
        $message = '';
        switch ($status) {

            case AuthConstants::LOGIN_FAILED:
                $message = sprintf($format, 'badge-light-warning',AuthConstants::LOGIN_MESSAGE[AuthConstants::LOGIN_FAILED]);
                break;

            case AuthConstants::LOGIN_SUCCESS:
                $message = sprintf($format, 'badge-light-success',AuthConstants::LOGIN_MESSAGE[AuthConstants::LOGIN_SUCCESS]);
                break;

            case AuthConstants::LOGIN_LOCKOUT:
                $message = sprintf($format, 'badge-light-danger',AuthConstants::LOGIN_MESSAGE[AuthConstants::LOGIN_LOCKOUT]);
                break;

            case AuthConstants::LOGIN_LOGOUT:
                $message = sprintf($format, 'badge-light-info',AuthConstants::LOGIN_MESSAGE[AuthConstants::LOGIN_LOGOUT]);
                break;

            default:
                break;
        }
        return $message;
    }

}
