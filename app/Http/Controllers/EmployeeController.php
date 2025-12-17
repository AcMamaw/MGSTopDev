<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Mail\SendCredentialsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['role', 'user'])->get();
        $roles     = Role::all();

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
            'alt_email'  => 'nullable|email|max:255',
            'contact_no' => 'required|string|max:20',
            'status'     => 'nullable|string|max:20',
            'pictures'   => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('pictures')) {
            $file     = $request->file('pictures');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/employees', $filename);
            $data['pictures'] = $filename;
        }

        // create employee (includes alt_email)
        $employee = Employee::create($data);

        // generate credentials
        $plainPassword = substr($employee->fname, 0, 3) . substr($employee->lname, 0, 3) . '123';
        $username      = $employee->email;   // you can change if you want

        // create user record
        $user = User::create([
            'employee_id'    => $employee->employee_id,
            'username'       => $username,
            'email'          => $username,
            'password'       => Hash::make($plainPassword),
            'plain_password' => $plainPassword,
        ]);

        // reload employee with role relation
        $employee = Employee::with('role')->find($employee->employee_id);

        // decide where to send
        $to = $employee->alt_email ?: $employee->email;

        if ($to) {
            // send email with credentials
            Mail::to($to)->send(new SendCredentialsMail([
                'email'    => $user->email,
                'username' => $user->username,
                'password' => $user->plain_password,
            ]));
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Employee added successfully!',
            'employee'       => $employee,
            'username'       => $username,
            'plain_password' => $plainPassword,
            'password_hash'  => $user->password,
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
                'alt_email'  => 'nullable|email|max:255',
                'contact_no' => 'required|string|max:20',
            ]);

            $employee->update($data);
            $employee->load('role');

            return response()->json($employee);
        } catch (\Throwable $e) {
            \Log::error('Employee update error', [
                'msg'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function archive($id)
    {
        $employee           = Employee::findOrFail($id);
        $employee->archive  = 'Archived';
        $employee->save();

        return response()->json(['status' => 'ok']);
    }

    public function unarchive($id)
    {
        $employee           = Employee::findOrFail($id);
        $employee->archive  = null;
        $employee->save();

        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(['status' => 'ok']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->firstOrFail();
        $employee = $user->employee; // needs relation

        $plain = substr($employee->fname, 0, 3) . substr($employee->lname, 0, 3) . rand(100, 999);

        $user->password       = Hash::make($plain);
        $user->plain_password = $plain;
        $user->save();

        $to = $employee->alt_email ?: $employee->email;

        if ($to) {
            Mail::to($to)->send(new SendCredentialsMail([
                'email'    => $user->email,
                'username' => $user->username,
                'password' => $plain,
            ]));
        }

        return response()->json(['status' => 'ok']);
    }
}
