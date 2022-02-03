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
    public function index(Request $request)
    {
        $employees = Employee::with('user', 'user.roles:id,name', 'designation:id,name');

        //Search the employees
        if ($request->q)
            $employees = $employees->where(function ($employees) use ($request) {
                //Search the data by name
                $employees = $employees->whereHas('user', fn ($q) => $q->where('name', 'LIKE', '%' . $request->q . '%'));

                //Search the data by designation
                $employees = $employees->whereHas('designation', fn ($q) => $q->orWhere('name', 'LIKE', '%' . $request->q . '%'));
            });


        //Ordering the collection
        $order = json_decode($request->get('order'));
        if (isset($order->column))
            $employees = $employees->where(function ($employees) use ($order) {

                // Order by name field
                if ($order->column == 'name')
                    $employees = $employees->whereHas('user', fn ($q) => $q->orderBy('name', $order->direction));

                // Order by name field
                if (isset($order->column) && $order->column == 'role')
                    $employees = $employees->whereHas('user.roles', fn ($q) => $q->orderBy('name', $order->direction));
            });

        //Paginate the collection
        $employees = $employees->paginate($request->get('rows', 10));

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
            'password' => 'nullable|string',
            'avatar' => 'nullable|image|max:1024',
            'designation_id' => 'required|exists:designations,id'
        ]);

        if ($request->hasFile('avatar'))
            $avatar = $request->file('avatar')->store('users/avatar');


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $avatar ?? null
        ]);



        $token = $user->createToken('auth_token')->plainTextToken;

        if ($request->designation_id)
            $user->employee()->create($request->all());

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => "Bearer",
            "message" => "User Created Successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(User $employee)
    {
        return EmployeeResource::make($employee);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $employee
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
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $employee)
    {
        try {


            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $employee->id,
                'password' => 'nullable|string',
                'avatar' => 'nullable|image|max:1024',
                'designation_id' => 'required|exists:designations,id',
                'role' => 'required|exists:roles,id',
            ]);
            //Collect data in variable
            $data = $request->only('name', 'email', 'avatar', 'designation_id');
            $data['status'] = $request->has('status');

            if ($request->password)
                $data['password'] = Hash::make($request->password);

            //Store logo if the file exists in the request
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('users/avatar');

                //Delete the previos logo if exists
                if (Storage::exists($employee->avatar))
                    Storage::delete($employee->avatar);
            }

            //Update employee
            $employee->employee->update($data);
            $employee->update($data);
            $request->role && $employee->roles()->sync($request->role);

            return message('Employee updated successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $employee)
    {
        if ($employee->delete())
            return message('Employee deleted successfully');

        return message('Something went wrong', 400);
    }
}
