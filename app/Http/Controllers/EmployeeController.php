<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeCollection;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::with('user', 'designation:id,name')->get();

        return EmployeeCollection::collection($employees);
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
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'avatar' => 'nullable|image|max:1024',
            'designation_id' => 'required'
        ]);

        if ($request->hasFile('avatar'))
            $avatar = $request->file('avatar')->store('users/avatar');


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'avatar' => $avatar ?? null
        ]);

        if ($request->designation_id)
            $user->employee()->create($request->all());

        return response()->json([

            "message" => "User Created Successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return EmployeeResource::make($employee);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        if (!$employee)
            return response()->json(['message' => 'Employee not found!'], 404);

        try {
            //Collect data in variable
            $data = $request->only('name', 'email', 'avatar');

            //Store logo if the file exists in the request
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('users/avatar');

                //Delete the previos logo if exists
                if (Storage::exists($employee->avatar))
                    Storage::delete($employee->avatar);
            }

            if ($request->password)
                $data['password'] = Hash::make($request->password);

            //Update employee
            $employee->update([
                'designation_id' => $request->designation_id
            ]);
            $employee->user()->update($data);

            return message('Employee updated successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        if ($employee->delete())
            return message('Employee deleted successfully');

        return message('Something went wrong', 400);
    }
}
