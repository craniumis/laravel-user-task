<?php
namespace App\Helpers;

use Illuminate\Http\Request;

class Helper{

    public static function file_upload(Request $request, string $field_name, string $path):string
    {
        $file_unique_name = $request->file($field_name)->hashName();
        //
        $request->file($field_name)->storePubliclyAs('public/'.$path, $file_unique_name);
        return $file_unique_name;
    }
}
