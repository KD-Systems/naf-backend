<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PartItem;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\CompanyMachine;
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
        $requisitions = Requisition::with(
            'company:id,name',
            'machines:id,machine_model_id',
            'machines.machineModel:id,name'
        );

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

        $machines = CompanyMachine::with('machineModel.machine.headings')->find($request->machine_ids);
        $headings = $machines->pluck('machineModel.machine.headings')->flatten();

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
        $request->validate([
            'part_items' => 'required|min:1',
            // 'expected_delivery' => 'required',
            'company_id' => 'required|exists:companies,id',
            'machine_id' => 'required|exists:company_machines,id',
            'engineer_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'payment_mode' => 'required_if:type,purchase_request',
            'payment_term' => 'required_if:type,purchase_request',
            'type'=>'required|in:claim_report,purchase_request',
            // 'payment_partial_mode' => 'required_if:payment_term,partial',
            'partial_time' => 'required_if:payment_term,partial',
            'next_payment' => 'required_if:payment_term,partial',
        ]);

        try {
            $data = $request->except('partItems');

            //Store the requisition data
            $requisition = Requisition::create($data);
            // create unique id
            $id = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();
            $data = Requisition::findOrFail($id);
            // $str = str_pad($id, 4, '0', STR_PAD_LEFT);  //custom id generate
            $data->update([
                'rq_number'   => 'RQ'.date("Ym").$id,
            ]);


            //Attach the machines to the requisition
            // $machines = implode(",", $data['machine_id']);

            $requisition->machines()->sync($data['machine_id']);

            $items = collect($request->part_items);
            $items = $items->map(function ($dt) {
                return [
                    'part_id' => $dt['id'],
                    'quantity' => $dt['quantity'],
                    'unit_value' => $dt['selling_price'],
                    'total_value' => $dt['quantity'] * $dt['selling_price']
                ];
            });

            $requisition->partItems()->createMany($items);

            return message('Requisition created successfully', 200, $requisition);
        } catch (\Throwable $th) {
            return message(
                $th->getMessage(),
                400
            );
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
        $requisition->load([
            'company',
            'machines:id,machine_model_id',
            'machines.machineModel:id,name',
            'engineer',
            'partItems.part.aliases'
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
}
