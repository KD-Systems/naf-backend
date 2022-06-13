<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return settings();
        $settings = Setting::all()
            ->pluck('value', 'key')
            ->map(function ($dt, $key) {
                in_array($key, ['logo', 'icon']) && $dt = asset($dt);
                return $dt;
            });

        // return $settings;
        return message(null, 200, $settings);
    }

    /**
     * Update/store data
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function store(Request $request)
    {

        // return $request->all();
        $settings = $request->only([
            'site_name',
            'notifiable_users',
            'notifiable_emails'
        ]);

        if (is_array($settings['notifiable_emails']))
            $settings['notifiable_emails'] = implode(',', $settings['notifiable_emails']);

        // taking icon ,logos key and vlaues in settings variable
        foreach ($request->allFiles() as $key => $file) {
            if ($hasData = setting($key))
                File::exists($hasData) && unlink(public_path($hasData));

            $settings[$key] = 'uploads/' . $file->store('settings');
        }

        //mapping keys and values
        $data = collect($settings)
            ->map(fn ($value, $key) => [
                'key' => $key,
                'value' => strval($value)
            ])
            ->values()
            ->toArray();

        Setting::upsert($data, ['key']); //storing data

        return message('Settings updated successfully');
    }


    public function getUsers(){
        $user = User::join('employees','employees.user_id','=','users.id')
        ->select('users.id as id','users.name as name')->get();

        return response()->json($user);
        // return['data',$user];
    }
}
