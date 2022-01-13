<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyResource;
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
        $users = $company->users;
        return UserResource::collection($users);
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

            //Store avatar if the file exists in the request
            if ($request->hasFile('avatar'))
                $userData['avatar'] = $request->file('avatar')->store('companies/user-avatars'); //Set the company logo path

            //Store user data
            $user = $company->users()->create($userData);

            $userData['company_id'] = $company->id;
            $user->details()->create($userData); //Create company user details model

            return message('User added successfully');
        } catch (\Throwable $th) {
            return message('Something went wrong', 400);
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
        return UserResource::make($user);
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
    public function update(Request $request, Company $company)
    {
        //Validate the submitted data
        $request->validate([
            'name' => 'required|unique:companies,name,' . $company->id . '|string|max:155',
            'company_group' => 'nullable|string|max:155',
            'machine_types' => 'nullable|string|max:155',
            'logo' => 'nullable|image|max:1024',
            'description' => 'nullable|string'
        ]);

        try {
            //Collect data in variable
            $data = $request->all();

            //Store logo if the file exists in the request
            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('companies/logo'); //Set the company logo path

                //Delete the previos logo if exists
                if (Storage::exists($company->logo))
                    Storage::delete($company->logo);
            }

            //Update the company
            $company->update($data);

            return message('Company updated successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Add a new user to the company
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUser(Request $request, Company $company)
    {
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

            //Store avatar if the file exists in the request
            if ($request->hasFile('avatar'))
                $userData['avatar'] = $request->file('avatar')->store('companies/user-avatars'); //Set the company logo path

            //Store user data
            $user = $company->users()->create($userData);

            $userData['company_id'] = $company->id;
            $user->details()->create($userData); //Create company user details model

            return message('User added successfully');
        } catch (\Throwable $th) {
            return message('Something went wrong', 400);
        }
    }

    /**
     * Remove the specified company from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        if ($company->delete())
            return message('Company archived successfully');

        return message('Something went wrong', 400);
    }
}
