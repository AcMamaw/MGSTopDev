<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User; // Import User

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['role', 'user'])->get(); 
        $roles = Role::all();
        return view('management.employee', compact('employees', 'roles'));
    }

    // Store new employee + auto create user account
    public function store(Request $request)
    {
        $data = $request->validate([
            'role_id' => 'required|exists:roles,role_id',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'bdate' => 'required|date',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'contact_no' => 'required|string|max:20',
            'status' => 'nullable|string|max:20',
            'pictures' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('pictures')) {
            $file = $request->file('pictures');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/employees', $filename);
            $data['pictures'] = $filename;
        }

        $employee = Employee::create($data);

        // Generate user password: first 3 letters of fname + first 3 letters of lname + 123
        $plainPassword = substr($employee->fname, 0, 3) . substr($employee->lname, 0, 3) . '123';
        $username = $employee->email; // username = employee email

        // Create user account
        User::create([
            'employee_id'    => $employee->employee_id,
            'username'       => $username,
            'email'          => $username,
            'password'       => bcrypt($plainPassword),
            'plain_password' => $plainPassword,
        ]);

        $employee = Employee::with('role')->find($employee->employee_id);

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully!',
            'employee' => $employee,
            'username' => $username,
            'plain_password' => $plainPassword
             
        ]);
    }
}
