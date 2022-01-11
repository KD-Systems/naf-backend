<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CompanyCollection;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();

        return CompanyResource::collection($companies);
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
     * Store a newly created company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate the submitted data
        $request->validate([
            'name' => 'required|unique:companies,name|string|max:155',
            'slug' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'description' => 'nullable|string'
        ]);

        //Collect data in variable
        $data = $request->all();

        //Store logo if the file exists in the request
        if ($request->hasFile('logo'))
            $data['logo'] = $request->file('logo')->store('companies/logo'); //Set the company logo path

        //Store the company
        Company::create($data);

        return message('Company created successfully');
    }

    /**
     * Display the specified company.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return CompanyResource::make($company);
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
            'logo' => 'nullable|image|max:1024',
            'slug' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

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
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:16',
            'phone' => 'nullable|string|max:20'
        ]);

        $userData = $request->all();

        //Store avatar if the file exists in the request
        if ($request->hasFile('avatar'))
            $userData['avatar'] = $request->file('avatar')->store('companies/user-avatars'); //Set the company logo path

        $company->users()->create($userData);

        return message('User added successfully');
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
