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
        $users = $company->users()->with(['details', 'details.designation:id,name'])->latest()->get();

        return CompanyUserCollection::collection($users);
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
            'phone' => 'nullable|string|max:20',
            'designation_id' => 'required|integer|exists:designations,id'
        ]);

        try {
            //Grab all the data
            $userData = $request->all();

            $userData['company_id'] = $company->id;

            //Store avatar if the file exists in the request
            if ($request->hasFile('avatar'))
                $userData['avatar'] = $request->file('avatar')->store('companies/user-avatars'); //Set the company logo path

            //Store user data
            $user = $company->users()->create($userData);
            $user->details->update($userData); //Update company user details model, as it's already created

            return message('Client updated successfully');
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
            'phone' => 'nullable|string|max:20',
            'designation_id' => 'required|integer|exists:designations,id',
        ]);

        try {
            //Grab all the data
            $userData = $request->only('name', 'avatar', 'email', 'phone', 'designation_id');
            $userData['company_id'] = $company->id; //Set the company id for the details
            $userData['status'] = boolval($request->status ?? false);

            //Store avatar if the file exists in the request
            if ($request->hasFile('avatar')) {
                $userData['avatar'] = $request->file('avatar')->store('companies/user-avatars'); //Set the company logo path

                //Delete the previos avatar if exists
                if (Storage::exists($user->avatar))
                    Storage::delete($user->avatar);
            }

            //Update user data
            $user->update($userData);
            $user->details->update($userData); //Update company user details data

            return message('Client updated successfully');
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
            return message('Client archived successfully');

        return message('Something went wrong', 400);
    }
}
