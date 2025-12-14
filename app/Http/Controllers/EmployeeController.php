<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // for secure hashing [web:384][web:391]

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['role', 'user'])->get();
        $roles = Role::all();
        return view('management.employee', compact('employees', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_id'    => 'required|exists:roles,role_id',
            'fname'      => 'required|string|max:255',
            'lname'      => 'required|string|max:255',
            'gender'     => 'required|in:Male,Female',
            'bdate'      => 'required|date',
            'email'      => 'nullable|email|max:255|unique:employees,email',
            'contact_no' => 'required|string|max:20',
            'status'     => 'nullable|string|max:20',
            'pictures'   => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('pictures')) {
            $file = $request->file('pictures');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/employees', $filename);
            $data['pictures'] = $filename;
        }

        $employee = Employee::create($data);

        // Generate password (plain + hash)
        $plainPassword = substr($employee->fname, 0, 3) . substr($employee->lname, 0, 3) . '123';
        $username = $employee->email; // username = employee email

        // Create user account with hash + plain_password column
        $user = User::create([
            'employee_id'   => $employee->employee_id,
            'username'      => $username,
            'email'         => $username,
            'password'      => Hash::make($plainPassword),   // hashed
            'plain_password'=> $plainPassword,               // plain (like in your screenshot)
        ]);

        $employee = Employee::with('role')->find($employee->employee_id);

        return response()->json([
            'success'        => true,
            'message'        => 'Employee added successfully!',
            'employee'       => $employee,
            'username'       => $username,
            'plain_password' => $plainPassword,              // so you can show it in modal
            'password_hash'  => $user->password,             // if you also want the hash
        ]);
    }
    public function update(Request $request, Employee $employee)
    {
        try {
            $data = $request->validate([
                'role_id'    => 'required|exists:roles,role_id',
                'fname'      => 'required|string|max:255',
                'lname'      => 'required|string|max:255',
                'gender'     => 'required|in:Male,Female',
                'bdate'      => 'required|date',
                'email'      => 'nullable|email|max:255|unique:employees,email,' . $employee->employee_id . ',employee_id',
                'contact_no' => 'required|string|max:20',
            ]);

            $employee->update($data);
            $employee->load('role');

            return response()->json($employee);
        } catch (\Throwable $e) {
            \Log::error('Employee update error', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }


        public function archive($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->archive = 'Archived';
        $employee->save();

        return response()->json(['status' => 'ok']);
    }

    public function unarchive($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->archive = null;
        $employee->save();

        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        // If you use soft deletes, call $employee->delete();
        // else hard delete:
        $employee->delete();

        return response()->json(['status' => 'ok']);
    }


}
