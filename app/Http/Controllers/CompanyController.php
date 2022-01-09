<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [];
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
            'logo' => 'nullable|image|max:1024',
            'description' => 'nullable|string'
        ]);

        //Collect data in variable
        $data = $request->all();

        //Store logo if the file exists in the request
        if ($request->hasFile('logo'))
            $data['logo'] = $request->file('logo')->store('companies/logo'); //Set the company logo path

        //Store the company
        $company = Company::create($data)->fresh();

        return CompanyResource::make($company);
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
            'description' => 'nullable|string'
        ]);

        //Collect data in variable
        $data = $request->all();

        //Store logo if the file exists in the request
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies/logo'); //Set the company logo path

            // $file = \Storage::get($company->logo);
        }

        //Update the company
        $company->update($data);

        return CompanyResource::make($company);
    }

    /**
     * Remove the specified company from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //
    }
}
