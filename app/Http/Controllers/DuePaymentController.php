<?php

namespace App\Http\Controllers;

use App\Models\DuePaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return DuePaymentHistory::with('company')
            ->whereCompanyId($request->id)
            ->latest()
            ->get();
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
            'company_id' => 'required',
            'amount' => 'required',
            'transaction_type' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $data['created_by'] = auth()->user()->id;
            $advance_payment = DuePaymentHistory::create($data);

            DB::commit();
            return message('Due payment added successfully', 200, $advance_payment);
        } catch (\Throwable $th) {
            DB::rollback();
            return message(
                $th->getMessage(),
                400
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($duePaymentHistoryId, Request $request)
    {
        $isRemoved = DuePaymentHistory::whereId($duePaymentHistoryId)
            ->whereCompanyId($request->companyId)
            ->delete();

        return message($isRemoved ? 'Information Removed successfully' : 'Information not found', $isRemoved ? 200 : 404);
    }
}
