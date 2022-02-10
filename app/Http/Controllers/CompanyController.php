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
        //Authorize the user
        abort_unless(access('companies_access'), 403);

        $companies = Company::all();

        return CompanyCollection::collection($companies);
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
        //Authorize the user
        abort_unless(access('companies_create'), 403);

        //Validate the submitted data
        $request->validate([
            'name' => 'required|unique:companies,name|string|max:155',
            'company_group' => 'nullable|string|max:155',
            'machine_types' => 'nullable|string|max:155',
            'logo' => 'nullable|image|max:1024',
            'description' => 'nullable|string'
        ]);

        try {
            //Collect data in variable
            $data = $request->all();

            //Store logo if the file exists in the request
            if ($request->hasFile('logo'))
                $data['logo'] = $request->file('logo')->store('companies/logo'); //Set the company logo path

            //Store the company
            Company::create($data);

            return message('Company created successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified company.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //Authorize the user
        abort_unless(access('companies_show'), 403);


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
        //Authorize the user
        abort_unless(access('companies_edit'), 403);

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
     * Remove the specified company from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //Authorize the user
        abort_unless(access('companies_delete'), 403);


        if ($company->delete())
            return message('Company archived successfully');

        return message('Something went wrong', 400);
    }
}
