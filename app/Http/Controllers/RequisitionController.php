<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\User;
use App\Models\PartItem;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\CompanyMachine;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Http\Resources\PartItemResource;
use App\Http\Resources\RequisitionResource;
use App\Http\Resources\PartHeadingCollection;
use App\Http\Resources\RequisitionCollection;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        abort_unless(access('requisitions_access'), 403);

        $requisitions = Requisition::with(
            'company:id,name',
            'machines:id,machine_model_id',
            'machines.model:id,name'
        );

        //Search the quatation
        if ($request->q)
            $requisitions = $requisitions->where(function ($requisitions) use ($request) {
                //Search the data by company name and id
                $requisitions = $requisitions->where('rq_number', 'LIKE', '%' . $request->q . '%');
            });

        //Check if request wants all data of the requisitions
        if ($request->rows == 'all')
            return RequisitionCollection::collection($requisitions->get());

        $requisitions = $requisitions->paginate($request->get('rows', 10));

        return RequisitionCollection::collection($requisitions);
    }

    /**
     * Display a listing of the resource of Englineers List.
     *
     * @return \Illuminate\Http\Response
     */
    public function engineers()
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    /**
     * Display a listing of the part headings.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function partHeadings(Request $request)
    {
        $request->validate([
            'machine_ids' => 'required|exists:company_machines,id'
        ]);

        $machines = CompanyMachine::with('model.machine.headings')->find($request->machine_ids);
        $headings = $machines->pluck('model.machine.headings')->flatten()->unique();

        return PartHeadingCollection::collection($headings);
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

        //Authorize the user
        abort_unless(access('requisitions_create'), 403);

        $request->validate([
            'part_items' => 'required|min:1',
            'expected_delivery' => 'required',
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

        // try {
        $data = $request->except('partItems');

        //Store the requisition data
        $requisition = Requisition::create($data);

        $requisition->machines()->sync($data['machine_id']);
            //taking part stock
        $parts = Part::with([
            'stocks' => fn ($q) => $q->where('unit_value', '>', 0)
        ])->find(collect($request->part_items)->pluck('id'));

        // return message($parts, 400);
        $reqItems = collect($request->part_items);
        $items = $reqItems->map(function ($dt) use ($parts) {
            $stock = $parts->find($dt['id'])->stocks->last();
            if (!$stock)
                return message('"' . $dt['name'] . '" is out of stock', 400)->throwResponse();

            return [
                'part_id' => $dt['id'],
                'quantity' => $dt['quantity'],
                'unit_value' => $stock->selling_price,
                'total_value' => $dt['quantity'] *  $stock->selling_price
            ];
        });
        //storing data in partItems
        $requisition->partItems()->createMany($items);
        //updating RQ number
        $id = $requisition->id;
        $data = Requisition::findOrFail($id);
        $data->update([
            'rq_number'   => 'RQ' . date("Ym") . $id,
        ]);
        DB::commit();
        return message('Requisition created successfully', 200, $requisition);
        // } catch (\Throwable $th) {
        //     DB::rollback();
        //     return message(
        //         $th->getMessage(),
        //         400
        //     );
        // }
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

    public function storeClientReqisition(Request $request){

        $request->validate([
            'part_items' => 'required|min:1',
            'expected_delivery' => 'required',
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

        // try {
        $data = $request->except('partItems');

        //Store the requisition data
        $requisition = Requisition::create($data);

        $requisition->machines()->sync($data['machine_id']);
            //taking part stock
        $parts = Part::with([
            'stocks' => fn ($q) => $q->where('unit_value', '>', 0)
        ])->find(collect($request->part_items)->pluck('id'));

        // return message($parts, 400);
        $reqItems = collect($request->part_items);
        $items = $reqItems->map(function ($dt) use ($parts) {
            $stock = $parts->find($dt['id'])->stocks->last();
            if (!$stock)
                return message('"' . $dt['name'] . '" is out of stock', 400)->throwResponse();

            return [
                'part_id' => $dt['id'],
                'quantity' => $dt['quantity'],
                'unit_value' => $stock->selling_price,
                'total_value' => $dt['quantity'] *  $stock->selling_price
            ];
        });
        //storing data in partItems
        $requisition->partItems()->createMany($items);
        //updating RQ number
        $id = $requisition->id;
        $data = Requisition::findOrFail($id);
        $data->update([
            'rq_number'   => 'RQ' . date("Ym") . $id,
        ]);
        DB::commit();
        return message('Requisition created successfully', 200, $requisition);
    }
}
