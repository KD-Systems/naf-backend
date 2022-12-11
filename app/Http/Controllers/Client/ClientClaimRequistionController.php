<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClaimRequisitionCollection;
use App\Http\Resources\RequiredRequisitionCollection;
use App\Models\RequiredPartRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientClaimRequistionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        // abort_unless(access('requisitions_access'), 403);
        $company = auth()->user()->details?->company;
        $requisitions = $company->requisitions()->with(
            'quotation',
            'company:id,name,logo',
            'machines:id,machine_model_id',
            'machines.model:id,name'
        )->where('type', 'claim_report')->latest();

        $requisitions = $requisitions->has('partItems');
        //Search the quatation 
        if ($request->q)
            $requisitions = $requisitions->where(function ($requisitions) use ($request) {
                //Search the data by company name and id
                $requisitions = $requisitions->where('rq_number', 'LIKE', '%' . $request->q . '%');
            });

        if ($request->type)
            $requisitions = $requisitions->where(function ($requisitions) use ($request) {
                //Search the data by company name and id
                $requisitions = $requisitions->where('type', $request->type);
            });

        if ($request->status)
            $requisitions = $requisitions->where(function ($requisitions) use ($request) {
                //Search the data by company name and id
                $requisitions = $requisitions->where('status', $request->status);
            });

        //Check if request wants all data of the requisitions
        if ($request->rows == 'all')
            return ClaimRequisitionCollection::collection($requisitions->get());

        $requisitions = $requisitions->paginate($request->get('rows', 10));

        return ClaimRequisitionCollection::collection($requisitions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // for client claim request
    public function ClientClaimRequest(Request $request)
    {

        $company = auth()->user()->details?->company;

        $requiredRequisition = $company->requiredRequisitions()->with('requiredPartItems', 'company', 'engineer', 'machines')->where('type', 'claim_report')->latest();

        //Search the quatation
        if ($request->q)
            $requiredRequisition = $requiredRequisition->where(function ($requiredRequisition) use ($request) {
                //Search the data by company name and id
                $requiredRequisition = $requiredRequisition->where('rr_number', 'LIKE', '%' . $request->q . '%');
            });

        if ($request->status)
            $requiredRequisition = $requiredRequisition->where(function ($requiredRequisition) use ($request) {
                //Search the data by company name and id
                $requiredRequisition = $requiredRequisition->where('status', $request->status);
            });

        if ($request->r_status == 'created')
            $requiredRequisition = $requiredRequisition->whereNotNull('requisition_id');

        if ($request->r_status == 'not_created')
            $requiredRequisition = $requiredRequisition->whereNull('requisition_id');

        if ($request->rows == 'all')
            return RequiredRequisitionCollection::collection($requiredRequisition->get());

        $requisitions = $requiredRequisition->paginate($request->get('rows', 10));

        return RequiredRequisitionCollection::collection($requisitions);
    }

    public function ClientClaimRequestCreate(Request $request)
    {

        $request->validate([
            'part_items' => 'required|min:1',
            // 'expected_delivery' => 'required',
            'company_id' => 'required|exists:companies,id',
            'machine_id' => 'required|exists:company_machines,id',
            'engineer_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'payment_mode' => 'required_if:type,purchase_request',
            'payment_term' => 'required_if:type,purchase_request',
            'type' => 'required|in:claim_report,purchase_request',
            // 'payment_partial_mode' => 'required_if:payment_term,partial',
            'partial_time' => 'required_if:payment_term,partial',
            'next_payment' => 'required_if:payment_term,partial',
        ]);

        DB::beginTransaction();

        try {

            $data = $request->except('requiredPartItems');
            //Set status
            $data['status'] = 'pending';
            $data['created_by'] = auth()->user()->id;
            $data['machine_id'] = implode(",", $request->machine_id);

            //Store the requisition data
            $requiredRequisition = RequiredPartRequisition::create($data);

            $reqItems = collect($request->part_items);
            //store data in required part items
            $requiredRequisition->requiredPartItems()->createMany($reqItems);

            DB::commit();
            return message('Required requisition created successfully', 200, $requiredRequisition);
        } catch (\Throwable $th) {
            DB::rollback();
            return message(
                $th->getMessage(),
                400
            );
        }
    }
}
