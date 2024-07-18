<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/05/22
 * Time: 22:12:36
 * Description: FileManager.php
 */

namespace App\Http\Helpers\Core;

use App\Http\Constants\FileDestinations;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Artisan;


class FileManager
{
    const FILE_TYPE_IMAGE = 1;
    const FILE_TYPE_DOCUMENTS = 2;
    const FILE_TYPE_PDF = 3;
    const FILE_TYPE_WORD = 4;
    const FILE_TYPE_EXCEL = 5;
    const FILE_TYPE_COMMON_MUTI_FILE = 6;
    const TEMP_UTL_EXPIRY_TIME = 10;
    const STORAGE_DISK = 'local';

    public function __construct()
    {

    }

    /**
     * Upload File To Storage Directory
     *
     * @param string $destination
     * @param string $fileName
     * @param int $validate
     * @param string $prefix
     * @return array|bool
     */
    public static function upload(string $destination = FileDestinations::COMMON, string $fileName = 'file', int $validate = self::FILE_TYPE_DOCUMENTS, string $prefix = ''): array
    {
        $response = [
            'status' => false,
            'message' => 'Failed - Invalid File',
            'data' => [],
        ];
        self::setSymbolicLink();
        if (self::validateFile($fileName, $validate)) {
            $orginalFileName = Request::file($fileName)->getClientOriginalName();
            $uploadedFileName = self::getCleanFilename($orginalFileName, $prefix);
            Storage::putFileAs($destination, Request::file($fileName), $uploadedFileName);
            $response['status'] = true;
            $response['message'] = 'File Upload Successfully';
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
     * Upload multi file
     *
     * @param string $destination
     * @param string $fileName
     * @param int $validate
     * @param string $prefix
     * @return array
     */
    public static function multiUpload(string $destination = FileDestinations::COMMON, string $fileName = 'file', int $validate = self::FILE_TYPE_COMMON_MUTI_FILE, string $prefix = ''): array
    {
        $response = [
            'status' => false,
            'message' => 'Failed - Invalid File',
            'data' => [],
        ];
        self::setSymbolicLink();
        if (Request::hasFile($fileName)) {
            if (self::validateFile($fileName, $validate)) {
                foreach (Request::file($fileName) as $file) {
                    $orginalFileName = $file->getClientOriginalName();
                    $uploadedFileName = self::getCleanFilename($orginalFileName, $prefix);
                    Storage::putFileAs($destination, $file, $uploadedFileName);
                    $response['data'][] = [
                        'orginalFileName' => $orginalFileName,
                        'fileName' => $uploadedFileName,
                        'size' => self::getFileSize($file->getSize()),
                        'extension' => $file->getClientOriginalExtension(),
                        'mimeType' => $file->getMimeType(),
                    ];
                }
                $response['status'] = true;
                $response['message'] = 'File Upload Successfully';
            }
        }
        return $response;
    }


    /**
     * Delete File From Storage Path
     *
     * @param string $fileName
     * @param string $destination
     */
    public static function delete(string $fileName, string $destination = FileDestinations::COMMON)
    {
        Storage::delete($destination . $fileName);
    }

    /**
     * Copy File
     *
     * @param string $from (Full Path)
     * @param string $to (storage directory)
     */
    public static function copy(string $from, string $to)
    {
//        Storage::copy($from, Storage::disk(self::STORAGE_DISK)->getDriver()->getAdapter()->getPathPrefix() . $to);
        copy($from, Storage::disk(self::STORAGE_DISK)->path($to));
    }

    /**
     * Move File
     *
     * @param string $from (Full Path)
     * @param string $to (Full Path)
     */
    public static function move(string $from, string $to)
    {
        Storage::move($from, $to);
    }

    /**
     * Get Full File URL
     *
     * @param string $fileName
     * @param string $destination
     * @return string
     */
    public static function getFileUrl($fileName, string $destination = FileDestinations::COMMON): string
    {
        return Storage::url($destination . $fileName);
    }

    /**
     * Get Storage path
     *
     * @param string $fileName
     * @param string $destination
     * @return string
     */
    public static function getStoragePath(string $fileName, string $destination = FileDestinations::COMMON): string
    {
        return Storage::disk(self::STORAGE_DISK)->path($destination . $fileName);
    }

    /**
     * Get Temporary URL For File
     *
     * @param string $fileName
     * @param string $destination
     * @return string
     */
    public static function getTemporaryUrl(string $fileName, string $destination = FileDestinations::COMMON): string
    {
        return Storage::temporaryUrl(
            $destination . $fileName, now()->addMinutes(self::TEMP_UTL_EXPIRY_TIME)
        );
    }

    /**
     * Download File
     *
     * @param string $fileName (Filename on which file is saved)
     * @param string $orginalFileName (Filename which was used while uploading the file / Expected Filename while downloading)
     * @param string $destination
     * @param array $headers (eg: 'Content-Type: application/pdf')
     * @return mixed
     */
    public static function downloadFile(string $fileName, string $orginalFileName, string $destination = FileDestinations::COMMON, $headers = [])
    {
        return Storage::download($destination . $fileName, $fileName, $headers);
    }

    /**
     * Validate File based on mime type
     *
     * @param string $fileName
     * @param int $type
     * @return boolean
     */
    protected static function validateFile($fileName = 'file', $type = self::FILE_TYPE_DOCUMENTS)
    {
        $status = true;
        $rule = 'required|file|mimes:xlsx,xls,csv,jpg,jpeg,png,bmp,doc,docx,pdf,tif,tiff,txt,rtf';
        if (self::FILE_TYPE_EXCEL == $type) {
            $UploadedFile = Request::file('file');
            $Extension = $UploadedFile->getClientOriginalExtension();
            $valid= ['csv','xls','xlsx','txt'];
            $status = in_array($Extension, $valid) ? true : false;
        } elseif (self::FILE_TYPE_COMMON_MUTI_FILE) {
            $validator = Validator::make(Request::all(), [
                $fileName . '.*' => 'mimes:doc,pdf,docx,zip,txt,jpg,jpeg,png,xlsx,xls,csv,tif,tiff,rtf',
            ]);
            if ($validator->fails()) {
                $status = false;
            }
        } else {
            if (self::FILE_TYPE_IMAGE == $type) {
                $rule = 'required|file|mimes:jpg,jpeg,png,bmp';
            } elseif (self::FILE_TYPE_PDF == $type) {
                $rule = 'required|file|mimes:pdf';
            } elseif (self::FILE_TYPE_WORD == $type) {
                $rule = 'required|file|mimes:doc,docx,tif,tiff';
            }
            $validator = Validator::make(Request::all(), [
                $fileName => $rule,
            ]);
            if ($validator->fails()) {
                $status = false;
            }
        }
        return $status;
    }

    /**
     * Generate SymbolicLink
     */
    protected static function setSymbolicLink()
    {
        if (! is_dir(public_path() . '/' . 'storage')) {
            Artisan::call('storage:link');
        }
    }

    /**
     * Create new directory if not exists
     * @param String $path
     */
    public static function createDirectory(String $path)
    {
        if (! is_dir(Storage::disk(self::STORAGE_DISK)->path($path))) {
            Storage::MakeDirectory($path);
        }
    }

    public static function checkFileExist(string $fileName, string $filePath): bool
    {
        return file_exists(self::getStoragePath($fileName, $filePath));
    }


    /**
     * get clean filename
     *
     * @param string $filename
     * @param string $prefix
     * @return string
     */
    public static function getCleanFilename($filename, $prefix = '')
    {
        $filename = preg_replace('/[^a-z0-9_.-]+/i', '', $filename);
        $filename = preg_replace('/[.]+/', '.', $filename);
        $filename = preg_replace('/[_-]+/', '_', $filename);
        if ('' != $prefix) {
            $filename = $prefix . '_' . time() . '_' . $filename;
        } else {
            $filename = time() . '_' . rand(1, 10000) . '_' . $filename;
        }
        return strtolower($filename);
    }

    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     */
    protected static function getFileSize($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = [
            0 => [
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ],
            1 => [
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ],
            2 => [
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ],
            3 => [
                "UNIT" => "KB",
                "VALUE" => 1024
            ],
            4 => [
                "UNIT" => "B",
                "VALUE" => 1
            ],
        ];
        $result = $bytes / 1;
        foreach($arBytes as $arItem) {
            if($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "." , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

}
