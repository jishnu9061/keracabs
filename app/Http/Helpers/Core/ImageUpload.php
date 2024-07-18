<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/05/22
 * Time: 22:13:09
 * Description: ImageUpload.php
 */

namespace App\Http\Helpers\Core;

use App\Http\Constants\FileDestinations;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Imagick;
use Intervention\Image\Facades\Image;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;

class ImageUpload extends FileManager {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Upload file from base64
     *
     * @param String $filename
     * @param String $path
     * @param array $options
     * @return array
     */
    public static function uploadBase64(string $fileName, string $path, array $options = []): array {
        $response = [
            'status' => false,
            'message' => 'Failed - Invalid File',
            'data' => [],
        ];
        self::setSymbolicLink();
        self::createDirectory($path);
        $base64Value = Request::input($fileName);
        if (self::validateBase64Image($base64Value)) {
            $image = Image::make($base64Value);
            $uploadedFileName = self::getFileNameForBase64Image($base64Value);
            $image->save(Storage::disk(self::STORAGE_DISK)->getDriver()->getAdapter()->getPathPrefix() . $path . $uploadedFileName);
            $response['status'] = true;
            $response['message'] = 'Image Upload Successfully';
            $extension = self::getFileFormatFromBase64($base64Value);
            $response['data'] = [
                'orginalFileName' => '',
                'fileName' => $uploadedFileName,
                'size' => self::getFileSize(self::getFileLengthForBase64Image($base64Value)),
                'extension' => $extension,
                'mimeType' => 'image/' . $extension,
            ];
        }
        return $response;
    }


    /**
     * Upload file from base64 core
     *
     * @param string $base64Value
     * @param string $path
     * @param array $options
     * @return array
     */
    public static function uploadBase64Core(string $base64Value, string $path, array $options = []): array {
        $response = [
            'status' => false,
            'message' => 'Failed - Invalid File',
            'data' => [],
        ];
        self::setSymbolicLink();
        self::createDirectory($path);
        if (self::validateBase64Image($base64Value)) {
            $image = Image::make($base64Value);
            $uploadedFileName = self::getFileNameForBase64Image($base64Value);
            $image->save(Storage::disk(self::STORAGE_DISK)->getDriver()->getAdapter()->getPathPrefix() . $path . $uploadedFileName);
            $response['status'] = true;
            $response['message'] = 'Image Upload Successfully';
            $extension = self::getFileFormatFromBase64($base64Value);
            $response['data'] = [
                'orginalFileName' => '',
                'fileName' => $uploadedFileName,
                'size' => self::getFileSize(self::getFileLengthForBase64Image($base64Value)),
                'extension' => $extension,
                'mimeType' => 'image/' . $extension,
            ];
        }
        return $response;
    }

    /**
     * Upload Images From Form Data
     *
     * @param string $fileName
     * @param string $destination
     * @param array $options
     * @return array
     */
    public static function uploadImage(string $fileName, string $destination, array $options = []): array {
        $response = [
            'status' => false,
            'message' => 'Failed - Invalid File',
            'data' => [],
        ];
        self::setSymbolicLink();
        self::createDirectory($path);

        if (self::validateFile($fileName, SELF::FILE_TYPE_IMAGE)) {
            $orginalFileName = Request::file($fileName)->getClientOriginalName();
            $uploadedFileName = self::getCleanFilename($orginalFileName);
            Storage::putFileAs($destination, Request::file($fileName), $uploadedFileName);
            $response['status'] = true;
            $response['message'] = 'Image Upload Successfully';
            $response['data'] = [
                'orginalFileName' => $orginalFileName,
                'fileName' => $uploadedFileName,
                'size' => self::getFileSize(Request::file($fileName)->getSize()),
                'extension' => Request::file($fileName)->getClientOriginalExtension(),
                'mimeType' => Request::file($fileName)->getMimeType(),
            ];
        }
        return $response;
    }

    /**
     * Validate Base64 Image
     *
     * @param String $value
     * @return bool
     */
    public static function validateBase64Image(string $value): bool {
        $status = true;
        if ('' != $value) {
            $explode = explode(',', $value);

            $allow = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
            $format = str_replace([
                'data:image/',
                ';',
                'base64',
            ], ['', '', '',], $explode[0]
            );
            if (!in_array($format, $allow)) {
                $status = false;
            }
            if (!empty($explode[1]) && !preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                $status = false;
            }
        }
        return $status;
    }

    /**
     * Get File Name from Base64 String
     *
     * @param String $value
     * @return string
     */
    private static function getFileNameForBase64Image(string $value): string {
        $random = self::generateRandomString(10);
        return time().$random. '.' . self::getFileFormatFromBase64($value);
    }

    /**
     * Get File Length from Base64 String
     * @param String $value
     * @return int
     */
    private static function getFileLengthForBase64Image(string $value): string {
        return strlen(base64_decode($value));
    }

    /**
     * Get File Format from Base64 String
     *
     * @param String $value
     * @return string
     */
    private static function getFileFormatFromBase64(string $value): string {
        $explode = explode(',', $value);
        return str_replace([
            'data:image/',
            ';',
            'base64',
        ], ['', '', '',], $explode[0]
        );
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $filename
     * @param string $newFileName
     * @param array $resolution
     * @return array
     * @throws \ImagickException
     */
    public static function generateImageFromPdf($filename, string $newFileName, array $resolution)
    {
        $im = new Imagick();
        $im->setResolution($resolution['x'], $resolution['y']);
        $im->readImage(Storage::disk(self::STORAGE_DISK)->getDriver()->getAdapter()->getPathPrefix() . $filename);
        $im->setImageCompressionQuality(100);
        $im->writeImage(Storage::disk(self::STORAGE_DISK)->getDriver()->getAdapter()->getPathPrefix() . $newFileName);
    }

}
