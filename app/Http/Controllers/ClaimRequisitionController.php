<?php

namespace App\Http\Controllers;

use App\Http\Resources\EngineerCollection;
use App\Models\Part;
use App\Models\User;
use App\Models\PartItem;
use App\Models\Quotation;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\CompanyMachine;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Http\Resources\PartItemResource;
use App\Http\Resources\RequisitionResource;
use App\Http\Resources\PartHeadingCollection;
use App\Http\Resources\ClaimRequisitionCollection;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\RequiredPartRequisition;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ClaimRequisitionController extends Controller
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

        $requisitions = Requisition::with(
            'requiredRequisition',
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
     * Display a listing of the resource of Englineers List.
     *
     * @return \Illuminate\Http\Response
     */
    // public function engineers()
    // {
    //    $users = User::whereHas('employee.designation', function($query){
    //     $query->where('name', 'Engineer');
    //    })->get();

    //     return EngineerCollection::collection($users);
    // }

    /**
     * Display a listing of the part headings.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // public function partHeadings(Request $request)
    // {
    //     $request->validate([
    //         'machine_ids' => 'required|exists:company_machines,id'
    //     ]);

    //     $machines = CompanyMachine::with('model.machine.headings')->find($request->machine_ids);
    //     $headings = $machines->pluck('model.machine.headings')->flatten()->unique();

    //     return PartHeadingCollection::collection($headings);
    // }

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
        //Authorize the user
        // abort_unless(access('requisitions_create'), 403);

        //If customer has any previous due
        if ($request->is_due) {

            $request->validate([
                'amount' => 'required|numeric|gt:0',
                'company_id' => 'required|exists:companies,id',

            ]);

            // return $request;
            $req = new Requisition();
            $req->company_id = $request->company_id;
            $req->priority = "high";
            $req->type = "previous_due";
            $req->remarks = $request?->remarks;
            $req->created_by = auth()->user()->id;
            $req->save();


            $quotation = new Quotation();
            $quotation->company_id = $request->company_id;
            $quotation->requisition_id = $req->id;
            $quotation->created_by = auth()->user()->id;
            $quotation->save();

            $data = new Invoice();
            $data->company_id = $request->company_id;
            $data->quotation_id = $quotation->id;
            $data->previous_due = $request->amount;
            $data->created_by = auth()->user()->id;
            $data->save();

            $com = Company::find($request->company_id);
            $com->update(['due_amount' => $com->due_amount + $request->amount]);

            return message('Due successfully', 200);
        } else {

            $request->validate([
                'part_items' => 'required|min:1',
                'company_id' => 'required|exists:companies,id',
                'machine_id' => 'required|exists:company_machines,id',
                'engineer_id' => 'nullable|exists:users,id',
                'priority' => 'required|in:low,medium,high',
                'payment_mode' => 'required_if:type,purchase_request',
                'payment_term' => 'required_if:type,purchase_request',
                'type' => 'required|in:claim_report,purchase_request',
                'next_payment' => 'required_if:payment_term,partial',
            ]);

            DB::beginTransaction();

            try {
                $data = $request->except('partItems');
                //Set status
                $data['status'] = 'approved';
                $data['created_by'] = auth()->user()->id;

                //Set attribute
                request()->request->add(['rq_number' => 'default']);

                //Store the requisition data
                $requisition = Requisition::create($data);

                $requisition->machines()->sync($data['machine_id']);
                //taking part stock
                $parts = Part::with([
                    'stocks' => fn ($q) => $q->where('unit_value', '>', 0)
                ])->find(collect($request->part_items)->pluck('id'));

                $reqItems = collect($request->part_items);
                $items = $reqItems->map(function ($dt) use ($parts) {
                    $stock = $parts->find($dt['id'])->stocks->last();

                    return [
                        'part_id' => $dt['id'],
                        'name' => $dt['name'],
                        'quantity' => $dt['quantity'],
                        'unit_value' => $stock->selling_price ?? null,
                        'total_value' => $dt['quantity'] *  ($stock->selling_price ?? 0),
                        'remarks' => $dt['remarks'] ?? '',
                        'status' => $dt['status'] ?? '',
                        'type' => 'foc',
                    ];
                });

                // $stockOutItems = $items->filter(fn ($dt) => !$dt['unit_value'])->values();
                // if ($stockOutItems->count())
                //     return message('"' . $stockOutItems[0]['name'] . '" is out of stock', 400);

                //storing data in partItems
                $requisition->partItems()->createMany($items);

                RequiredPartRequisition::where("rr_number", $request->rr_number)->update([
                    "status" => $request->status,
                    "requisition_id" => $requisition->id,
                ]);

                DB::commit();
                return message('Requisition created successfully', 200, $requisition);
            } catch (\Throwable $th) {
                DB::rollback();
                return message(
                    $th->getMessage(),
                    400
                );
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function show(Requisition $requisition)
    {
        //Authorize the user
        abort_unless(access('requisitions_show'), 403);

        $requisition->load([
            'quotation',
            'company',
            'machines:id,machine_model_id',
            'machines.model:id,name',
            'engineer',
            'partItems.part.aliases',
            'partItems.part.stocks' => function ($q) {
                $q->where('unit_value', '>', 0);
            }
        ]);

        return RequisitionResource::make($requisition);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function edit(Requisition $requisition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function update(
        Request $request,
        Requisition $requisition
    ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requisition $requisition)
    {
        //
    }

    // public function storeClientReqisition(Request $request)
    // {

    //     // return $request->part_items->remarks;

    //     $request->validate([
    //         'part_items' => 'required|min:1',
    //         'expected_delivery' => 'required',
    //         'company_id' => 'required|exists:companies,id',
    //         'machine_id' => 'required|exists:company_machines,id', 
    //         'engineer_id' => 'nullable|exists:users,id',
    //         'priority' => 'required|in:low,medium,high',
    //         'payment_mode' => 'required_if:type,purchase_request',
    //         'payment_term' => 'required_if:type,purchase_request',
    //         'type' => 'required|in:claim_report,purchase_request',
    //         // 'payment_partial_mode' => 'required_if:payment_term,partial',
    //         'partial_time' => 'required_if:payment_term,partial',
    //         'next_payment' => 'required_if:payment_term,partial',
    //     ]);

    //     DB::beginTransaction();

    //     // try {
    //     $data = $request->except('partItems');

    //     //Store the requisition data
    //     $requisition = Requisition::create($data);

    //     $requisition->machines()->sync($data['machine_id']);
    //     //taking part stock
    //     $parts = Part::with([
    //         'stocks' => fn ($q) => $q->where('unit_value', '>', 0)
    //     ])->find(collect($request->part_items)->pluck('id'));

    //     // return message($parts, 400);
    //     $reqItems = collect($request->part_items);
    //     $items = $reqItems->map(function ($dt) use ($parts) {
    //         $stock = $parts->find($dt['id'])->stocks->last();
    //         if (!$stock)
    //             return message('"' . $dt['name'] . '" is out of stock', 400)->throwResponse();

    //         return [
    //             'part_id' => $dt['id'],
    //             'quantity' => $dt['quantity'],
    //             'unit_value' => $stock->selling_price,
    //             'total_value' => $dt['quantity'] *  $stock->selling_price,
    //             'remarks' => $dt['remarks'] ?? ''
    //         ];
    //     });
    //     // $items['remarks'] = $request->part_items;
    //     //storing data in partItems
    //     $requisition->partItems()->createMany($items);
    //     //updating RQ number
    //     $id = $requisition->id;
    //     $data = Requisition::findOrFail($id);
    //     $data->update([
    //         'rq_number'   => 'RQ' . date("Ym") . $id,
    //     ]);
    //     DB::commit();
    //     return message('Requisition created successfully', 200, $requisition);
    // }

    /**
     * Set approved requisition
     *
     * @param \App\Models\Requisition $requisition
     * @return \Illuminate\Http\JsonResponse
     */
    // public function approve(Requisition $requisition)
    // {
    //     $requisition->update([
    //         'status'   => "approved",
    //     ]);

    //     return message('Requisition aprroved', 200);
    // }

    /**
     * Set reject requisition
     *
     * @param \App\Models\Requisition $requisition
     * @return \Illuminate\Http\JsonResponse
     */
    // public function reject(Requisition $requisition)
    // {
    //     $requisition->update([
    //         'status'   => "rejected",
    //     ]);

    //     return message('Requisition rejected', 200);
    // }

    /**
     * Upload files and associate with the requisition
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Requisition $requisition
     * @return void
     */
    // public function uploadFiles(Request $request, Requisition $requisition)
    // {
    //      $request->validate([
    //         'files' => 'required|array',
    //         'files.*' => 'required|mimes:png,jpg,pdf,xlsx,xls,csv,doc,docx,txt,zip'
    //     ]);
    //     foreach ($request->file('files') as $file)
    //         $requisition->addMedia($file)
    //             ->preservingOriginal()
    //             ->toMediaCollection('requisition-files');

    //     return message('Files uploaded successfully');
    // }

    // public function getFiles(Requisition $requisition)
    // {
    //     $file = $requisition->getMedia('requisition-files')->toArray();

    //     return ['data' => $file];
    // }

    // public function deleteFiles(Request $request, Requisition $requisition, Media $media)
    // {
    //    $requisition->deleteMedia($media);
    //    return message('Files deleted successfully');
    // }

    public function updateFocPartInfo(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'remarks' => 'required'
        ]);
        $data = PartItem::findOrFail($id);
        $data->update([
            'status'   => $request->status,
            'remarks'   => $request->remarks,
        ]);

        return message('Information changes successfully', 200, $data);
    }
}
