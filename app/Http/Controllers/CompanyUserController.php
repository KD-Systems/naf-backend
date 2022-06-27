<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyUserCollection;
use App\Http\Resources\CompanyUserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CompanyUserController extends Controller
{
    /**
     * Display a listing of the companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        abort_unless(access('companies_users_access'), 403);
        $users = $company->users()->with('details')->get();

        return CompanyUserCollection::collection($users);
    }

    /**
     * Show the form for creating a new company.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Add a new user to the company
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Company $company)
    {
        abort_unless(access('companies_users_create'), 403);

        $request->validate([
            'name' => 'required|string|max:155',
            'avatar' => 'nullable|image|max:1024',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:16',
            'phone' => 'nullable|string|max:20'
        ]);

        try {
            //Grab all the data
            $userData = $request->all();

            $userData['company_id'] = $company->id;
            $userData['password'] = Hash::make($request->password);

            //Store avatar if the file exists in the request
            if ($request->hasFile('avatar'))
                $userData['avatar'] = $request->file('avatar')->store('companies/user-avatars'); //Set the company logo path

            //Store user data
            $user = $company->users()->create($userData);
            $user->details->update($userData); //Update company user details model, as it's already created

            return message('User updated successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company, User $user)
    {
        abort_unless(access('companies_users_show'), 403);

        return CompanyUserResource::make($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company, User $user)
    {
        abort_unless(access('companies_users_update'), 403);

        $request->validate([
            'name' => 'required|string|max:155',
            'avatar' => 'nullable|image|max:1024',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|max:16',
            'phone' => 'nullable|string|max:20'
        ]);

        try {
            //Grab all the data
            $userData = $request->only('name', 'avatar', 'email', 'phone');
            $userData['company_id'] = $company->id; //Set the company id for the details
            $userData['status'] = boolval($request->status ?? false);

            //Store avatar if the file exists in the request
            if ($request->hasFile('avatar')) {
                $userData['avatar'] = $request->file('avatar')->store('companies/user-avatars'); //Set the company logo path

                //Delete the previos avatar if exists
                if (Storage::exists($user->avatar))
                    Storage::delete($user->avatar);
            }

            //Check if the request contains password, then update it
            if ($request->password)
                $userData['password'] = Hash::make($request->password);

            //Update user data
            $user->update($userData);
            $user->details->update($userData); //Update company user details data

            return message('User updated successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified company from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company, User $user)
    {
        if ($user->delete())
            return message('User archived successfully');

        return message('Something went wrong', 400);
    }
}
