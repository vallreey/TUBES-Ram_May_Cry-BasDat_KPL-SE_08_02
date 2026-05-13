<?php

namespace App\Helpers;

class FileUploadHelper
{
    public static function upload($file, $folder)
    {
        return $file->store($folder, 'public');
    }
}