<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = Auth::user();

        return ProfileResource::make($user);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user = user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password does not match!');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return message('Password changed successfully', 200);
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'avatar' => 'nullable|image|max:1024',
            'password' => 'nullable',
        ]);

        $user = user();
        $data = $request->only('name', 'email', 'avatar');
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('users/avatar');

            //Delete the previos logo if exists
            if (Storage::exists($user->avatar))
                Storage::delete($user->avatar);
        }

        $user->update($data);

        return message('User updated successfully', 200);
    }
}
