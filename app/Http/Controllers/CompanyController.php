<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        //Authorize the user
        abort_unless(access('companies_access'), 403);

        $companies = Company::with('contracts')
            ->withCount(['contracts' => function ($q) {
                return $q->active();
            }]);

        //Check if request wants all data of the companies
        if ($request->rows == 'all')
            return CompanyCollection::collection($companies->get());

        //Search the companies
        if ($request->q)
            $companies = $companies->where(function ($p) use ($request) {
                //Search name
                $p = $p->where('name', 'LIKE', '%' . $request->q . '%');

                //Search company group
                $p = $p->orWhere('company_group', 'LIKE', '%' . $request->q . '%');

                //Search machine types
                $p = $p->orWhere('machine_types', 'LIKE', '%' . $request->q . '%');
            });

        //Ordering the collection
        $order = json_decode($request->get('order'));
        if (isset($order->column)) {

            //Order by name, company group and machine types
            if (in_array($order->column, ['name', 'company_group', 'machine_types']))
                $companies = $companies->orderBy($order->column, $order->direction);

            //Order by status
            if ($order->column == 'status')
                $companies = $companies->orderBy('contracts_count', $order->direction);
        }

        //Paginate the data
        $companies = $companies->paginate($request->get('rows', 10));

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
        // return $request->logo;
        //Authorize the user
        abort_unless(access('companies_create'), 403);

        //Validate the submitted data
        $request->validate([
            'name' => 'required|unique:companies,name|string|max:155',
            'company_group' => 'nullable|string|max:155',
            'machine_types' => 'nullable|string|max:155',
            // 'logo' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'trade_limit' => 'required'
        ]);

        try {

            //Store logo if the file exists in the request
            // if ($request->hasFile('logo'))
            //     $logo = $request->file('logo')->store('companies/logo'); //Set the company logo path

                if ($request->hasFile('logo'))
                    $logo = time().'.'.$request->logo->extension();
                    $request->logo->move(public_path('/uploads/companies/logo/'), $logo);


            //Store the company
            // Company::create($data);
            $company = Company::create([
                'name' => $request->name,
                'company_group' => $request->company_group,
                'machine_types' => $request->machine_types,
                'description' => $request->description,
                'logo' => $logo ?? null,
                'tel' => $request->tel,
                'email' => $request->email,
                'web' => $request->web,
                'trade_limit' => $request->trade_limit,
                'due_amount' => 0,
            ]);

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
            // return $data;

            // Store logo if the file exists in the request
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

    // get client company
    public function getClientCompany()
    {
        $company = auth()->user()->details?->company;
        return ['data' => $company];
    }

    // get client machines
    public function getClientMachines()
    {

        $machines = auth()->user()->details()
            ->with('company.machines.model')
            ->get()
            ->pluck('company.machines')
            ->flatten()
            ->pluck('model');
        return ['data' => $machines];
    }
    //update trade limit
    public function updateDueLimit(Request $request, Company $company)
    {
        // return $request->only('trade_limit', 'due_amount');

        if ($company->update($request->only('trade_limit', 'due_amount', 'remarks')))
            return message('Updated successfully');

        return message('Something went wrong', 400);
    }
}
