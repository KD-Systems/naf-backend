<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\RequisitionCollection;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requisitions = Requisition::all();

        return RequisitionCollection::collection($requisitions);
    }


      /**
     * Display a listing of the resource of Englineers List.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEnginners()
    {
        $users = User::all();
        return UserResource::collection($users);
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
            'payment_mode' => 'required_if:type,purchase_request',
            'payment_term' => 'required_if:type,purchase_request',
            // 'payment_partial_mode' => 'required_if:payment_term,partial',
            'partial_time' => 'required_if:payment_term,partial',
            'next_payment' => 'required_if:payment_term,partial',
        ]);
        try {
            $data = $request->except('partItems');
            if ($data['machine_id']){
                $data['machine_id'] = implode(",",($data['machine_id']));
            }
            $requisition = Requisition::create($data);

            $items = collect($request->partItems);
            $items = $items->map(function($dt) {
                return [
                    'part_id' => $dt['id'],
                    'quantity' => $dt['quantity'],
                    'unit_value' => $dt['selling_price'],
                    'total_value' => $dt['quantity'] * $dt['selling_price']
                ];
            });

            $requisition->partItems()->createMany($items);

            // $model = $machine->models()->create($data);

            return message('Requisition created successfully', 200, $requisition);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
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
        //
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
    public function update(Request $request, Requisition $requisition)
    {
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
