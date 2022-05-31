<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Update/store data
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function store(Request $request)
    {

    //    return $request->file('icon')->getClientOriginalExtension();

    //     if($request->hasFile('logo'))
    //     {
    //         $file = $request->file('logo');
    //         $extention = $file->getClientOriginalExtension();
    //         $fileName = time() .'.'.$extention;
    //         $file->move('uploads/logo-icons/',$fileName);

    //     }
    //     if($request->hasFile('icon'))
    //     {
    //         $file = $request->file('icon');
    //         $extention = $file->getClientOriginalExtension();
    //         $fileName = time() .'.'.$extention;
    //         $file->move('uploads/logo-icons/',$fileName);

    //     }

        $data = collect($request->all())
        ->map(fn($value, $key) => [
            'key' => $key,
            'value' => strval($value)
        ])
        ->values()
        ->toArray();
        Setting::upsert($data, ['key']);






        // return \Storage::put('uploads/' . 'Test' , $request->file('logo'));
        // return  $request->file('logo')->storeAs(public_path('settings'), 'test');
        // $settings = [];
        // foreach ($request->allFiles() as $key => $file) {
        //     $settings[$key] = $request->file('logo')->store('settings');
        // }
        // return $settings;
    }
}
