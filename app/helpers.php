<?php

use App\Models\File;
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
    return response()->json(['message' => $message, 'data' => $data, 'status' => $statusCode], $statusCode);
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
        $url = asset('uploads/' . $file);
    else
        $url = 'https://i2.wp.com/ui-avatars.com/api/' . Str::slug($name) . '/400';

    return $url;
}

function uploadFile($requestFiles, $path = '', $remarks = null)
{
    $files = [];
    if (is_array($requestFiles))
        foreach ($requestFiles as $key => $file) {
            $ext = $file->getClientOriginalExtension();
            $size = $file->getSize() / 1024; //Convert to KB
            $loc = $file->store($path);
            $nameExt = $file->getClientOriginalName();
            $name = pathinfo($nameExt, PATHINFO_FILENAME);

            $files[$key] = File::create([
                'file_name' => $name,
                'file_ext' => $ext,
                'file_size' => $size,
                'path' => $loc,
                'remarks' => $remarks
            ]);
        }

    else
        $files = $requestFiles->store($path);

    return $files;
}

function createFile($key, $file, $path, $remarks)
{
    $ext = $file->getClientOriginalExtension();
    $size = $file->getSize() / 1024; //Convert to KB
    $loc = $file->store($path);
    $nameExt = $file->getClientOriginalName();
    $name = pathinfo($nameExt, PATHINFO_FILENAME);

    $files[$key] = File::create([
        'file_name' => $name,
        'file_ext' => $ext,
        'file_size' => $size,
        'path' => $loc,
        'remarks' => $remarks
    ]);
}
