<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Str;

/**
 * Description of ApiBaseController
 *
 * @author Jishnu Tk
 */
class ApiBaseController extends Controller
{

    protected $_token;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->_token = $this->getAccessToken($request);
    }

    protected function getCollectionValues($value)
    {
        if (! is_array($value) && ! is_null($value)) {
            $value = explode(',', str_replace(['[', ']'], '', $value));
        }
        return $value;
    }

    /**
     * Send success response without token
     *
     * @param type $data
     * @param type $message
     * @param type $status
     * @return \Illuminate\Http\Response
     */
    protected function sendResponseWithoutToken($data = [], $message = '', $status = true)
    {
        $response = [
            'status' => $status,
            'data'    => $data,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * Send success response
     *
     * @param type $data
     * @param type $message
     * @param type $status
     * @return \Illuminate\Http\Response
     */
    protected function sendResponse($data = [], $message = '', $status = true)
    {
    	$response = [
            'status' => $status,
            'data'    => $data,
            'message' => $message,
            'access_token' => $this->_token,
            'token_type' => 'bearer'
        ];
        return response()->json($response, 200);
    }

    /**
     * Send success response
     *
     * @param type $resource
     * @param type $message
     * @param type $status
     * @return \Illuminate\Http\Response
     */
    protected function sendResponseWithResource($resource, $message = '', $status = true)
    {
        $response = [
            'status' => $status,
            'data'    => $resource,
            'message' => $message,
            'access_token' => $this->_token,
            'token_type' => 'bearer'
        ];
        return response()->json($response, 200);
    }

    /**
     * Send error response
     *
     * @param $message
     * @param array $errorTrace
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendError($message, $errorTrace = [], $code = 200)
    {
    	$response = [
            'status' => false,
            'message' => $message,
        ];

        if (! empty($errorTrace)) {
            $response['data'] = $errorTrace;
            $errorMessage = [];
            foreach ($errorTrace as $error) {
                $errorMessage[] = isset($error[0])? str_replace('.', '', $error[0]) : '';
            }
            $response['message'] = implode(', ', $errorMessage);
        }

        return response()->json($response, $code);
    }

    /**
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($data = [], $message = '')
    {
        $returnData = [
            'status' => true,
            'access_token' => $this->_token,
            'token_type' => 'bearer',
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($returnData);
    }

    /**
     * Get Logged user details
     *
     * @return object
     */
    protected function getLoggedUserDetails()
    {
        return auth()->user();
    }

    /**
     * Get Logged User Id
     *
     * @return object
     */
    protected function getLoggedUserId()
    {
        return auth()->user()->id;
    }

    /**
     * Get Logged User Id
     *
     * @return object
     */
    protected function getLoggedUserTimezone()
    {
        return (is_null(auth()->user()->timezone) || ('' == auth()->user()->timezone)) ? 'UTC' : auth()->user()->timezone;
    }

    /**
     * Get Access token
     *
     * @param Request $request
     * @return type
     */
    protected function getAccessToken(Request $request)
    {
        $header = $request->header('Authorization');
        return (Str::startsWith($header, 'Bearer '))? Str::substr($header, 7) : '';
    }

    protected function generateAccessToken()
    {
        $user = auth()->user();
        return $user->createToken(env('API_TOKEN_NAME', 'api-name'))->accessToken;
    }

}
