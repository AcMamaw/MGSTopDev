<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Role; // <-- Import Role

class EmployeeController extends Controller
{
     public function index()
    {
        $employees = Employee::with('role')->get();
        $roles = Role::all(); // fetch all roles
        return view('management.employee', compact('employees', 'roles'));
    }

    // Store new employee
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

        // Handle image upload if exists
        if ($request->hasFile('pictures')) {
            $file = $request->file('pictures');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/employees', $filename);
            $data['pictures'] = $filename;
        }

        // Create employee
        $employee = Employee::create($data);

        // Reload employee with role relation
        $employee = Employee::with('role')->find($employee->employee_id);

        // JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully!',
            'employee' => $employee
        ]);
    }

}
