<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * Message response for the API
 *
 * @param string $message Message to return
 * @param int $statusCode Response code
 * @return \Illuminate\Http\JsonResponse
 */
function message($message = "Operation successful", $statusCode = 200, $data = [])
{
    return response()->json(['message' => $message, 'data' => $data], $statusCode);
}

/**
 * Image URL generating
 *
 * @param mixed $file File including path
 * @param string $name Default name to create placeholder image
 * @return string URL of the file
 */
function image($file, $name = 'Avatar')
{
    if (Storage::exists($file))
        $url = asset('uploads/'.$file);
    else
        $url = 'https://i2.wp.com/ui-avatars.com/api/' . Str::slug($name) . '/400';

    return $url;
}
