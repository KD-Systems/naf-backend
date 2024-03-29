<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequiredRequisitionCollection;
use App\Http\Resources\RequiredRequisitionResource;
use App\Models\CompanyMachine;
use App\Models\RequiredPartItems;
use App\Models\RequiredPartRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RequiredPartRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requiredRequisition = RequiredPartRequisition::with('requisitions','requiredPartItems', 'company', 'engineer', 'machines','user')->where('type', 'purchase_request')->latest();

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
            $message = $requiredRequisition->type == 'claim_report' ? 'A new claim request created successfully': 'Required requisition created successfully';
            DB::commit();
            return message($message, 200, $requiredRequisition);
        } catch (\Throwable $th) {
            DB::rollback();
            return message(
                $th->getMessage(),
                400
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $requiredPartRequisition = RequiredPartRequisition::with(['requisitions','requiredPartItems', 'engineer', 'company', 'machines','user'])->where('id', $id)->first();
        $machine_ids = explode(",", $requiredPartRequisition['machine_id']);
        $requiredPartRequisition['machines_data'] = CompanyMachine::with('model')->whereIn('id', $machine_ids)->get();
        return RequiredRequisitionResource::make($requiredPartRequisition);
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
        $requisition = RequiredPartRequisition::find($id);
        if($requisition){
            $requisition->delete();
            RequiredPartItems::where('model_id',$id)->delete();
                return message('Requisition deleted successfully',201);
        }else{
            return message('Requisition deleted successfully',422);

        }
        
    }

    public function RequiredRequisitionStatus(Request $request, $id)
    {

        $data = RequiredPartRequisition::findOrFail($id);
        $data->update([
            'status'   => $request->status,
        ]);

        return message('Status changes successfully', 200, $data);
    }

    // requisiton info
    public function RequiredRequisitionInfo(Request $request, $id)
    {
        $data = RequiredPartRequisition::findOrFail($id);
        $data->update([
            'expected_delivery'   => $request->expected_delivery,
            'remarks'   => $request->remarks,
        ]);

        return message('Information changes successfully', 200, $data);
    }

    public function getRequestedPart($id){
       return RequiredPartItems::find($id);
    }

    public function updateRequestedPart(Request $request, $id)
    {
        $data = RequiredPartItems::findOrFail($id);
        $data->update([
            'status'   => $request->status,
            'remarks'   => $request->remarks,
        ]);

        return message('Information changes successfully', 200, $data);
    }

    // public function ClientRequiredRequisition(Request $request)
    // {
    //     $company = auth()->user()->details?->company;
    //     if ($company)
    //         $requiredRequisition = $company->requiredRequisitions()->with('requiredPartItems', 'company', 'engineer', 'machines')->where('type','purchase_request')->latest();

    //     //Search the quatation
    //     if ($request->q)
    //         $requiredRequisition = $requiredRequisition->where(function ($requiredRequisition) use ($request) {
    //             //Search the data by company name and id
    //             $requiredRequisition = $requiredRequisition->where('rr_number', 'LIKE', '%' . $request->q . '%');
    //         });

    //     if ($request->status)
    //         $requiredRequisition = $requiredRequisition->where(function ($requiredRequisition) use ($request) {
    //             //Search the data by company name and id
    //             $requiredRequisition = $requiredRequisition->where('status', $request->status);
    //         });

    //     if ($request->r_status == 'created')
    //         $requiredRequisition = $requiredRequisition->whereNotNull('requisition_id');

    //     if ($request->r_status == 'not_created')
    //         $requiredRequisition = $requiredRequisition->whereNull('requisition_id');

    //     if ($request->rows == 'all')
    //         return RequiredRequisitionCollection::collection($requiredRequisition->get());

    //     $requisitions = $requiredRequisition->paginate($request->get('rows', 10));

    //     return RequiredRequisitionCollection::collection($requisitions);
    // }

    public function ClaimRequest(Request $request)
    {
        $requiredRequisition = RequiredPartRequisition::with('requiredPartItems', 'company', 'engineer', 'machines')->where('type', 'claim_report')->latest();

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

    /**RequiredPartRequisition Files functanality**/
    //cliam request file
    public function uploadFiles(Request $request, $id)
    {
        // return $id;
        $requiredPartRequisition = RequiredPartRequisition::findOrFail($id);
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|mimes:png,jpg,pdf,xlsx,xls,csv,doc,docx,txt,zip'
        ]);
        foreach ($request->file('files') as $file)
            $requiredPartRequisition->addMedia($file)
                ->preservingOriginal()
                ->toMediaCollection('claim-request-part-files');

        return message('Files uploaded successfully');
    }
    //cliam request file
    public function getFiles($id)
    {
        $requiredPartRequisition = RequiredPartRequisition::findOrFail($id);
        $file = $requiredPartRequisition->getMedia('claim-request-part-files')->toArray();
        return ['data' => $file];
    }
    //cliam request file
    public function deleteFiles(Request $request, $id, Media $media)
    {
        $requiredPartRequisition = RequiredPartRequisition::findOrFail($id);
        $requiredPartRequisition->deleteMedia($media);
        return message('Files deleted successfully');
    }


    
    //required file
    public function requiredUploadFiles(Request $request, $id)
    {
        // return $id;
        $requiredPartRequisition = RequiredPartRequisition::findOrFail($id);
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|mimes:png,jpg,pdf,xlsx,xls,csv,doc,docx,txt,zip'
        ]);
        foreach ($request->file('files') as $file)
            $requiredPartRequisition->addMedia($file)
                ->preservingOriginal()
                ->toMediaCollection('required-part-files');

        return message('Files uploaded successfully');
    }
    //required file
    public function requiredGetFiles($id)
    {
        $requiredPartRequisition = RequiredPartRequisition::findOrFail($id);
        $file = $requiredPartRequisition->getMedia('required-part-files')->toArray();
        return ['data' => $file];
    }
    //required file
    public function requiredDeleteFiles(Request $request, $id, Media $media)
    {
        $requiredPartRequisition = RequiredPartRequisition::findOrFail($id);
        $requiredPartRequisition->deleteMedia($media);
        return message('Files deleted successfully');
    }

}
