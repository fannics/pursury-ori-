<?php
/**
 * Created by PhpStorm.
 * User: te7a
 * Date: 06/06/17
 * Time: 02:26 م
 */

namespace ProjectCarrasco\Helpers;

class Uploader {

    public static function upload($file,$path)
    {
        $extension = $file->getClientOriginalExtension();

        $oldFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $fileName = $oldFileName .'-'. str_random(10).'.'.$extension;

        $file->move($path,$fileName);

        return $fileName;
    }
}