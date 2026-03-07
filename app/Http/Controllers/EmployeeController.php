<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Mail\SendCredentialsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Services\FileUploadService;

class EmployeeController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

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
            $uploadResult = $this->fileUploadService->uploadEmployeePicture($request->file('pictures'));
            if ($uploadResult['success']) {
                $data['pictures'] = $uploadResult['url'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload profile picture: ' . $uploadResult['error']
                ], 500);
            }
        }

        // Create employee
        $employee = Employee::create($data);

        // Generate credentials
        $plainPassword = substr($employee->fname, 0, 3) . substr($employee->lname, 0, 3) . '123';
        $username      = $employee->email;

        // Create user record
        $user = User::create([
            'employee_id'    => $employee->employee_id,
            'username'       => $username,
            'email'          => $username,
            'password'       => Hash::make($plainPassword),
            'plain_password' => $plainPassword,
        ]);

        // Reload employee with role relation
        $employee = Employee::with('role')->find($employee->employee_id);

        // ✅ Send to alt_email first, fallback to email
        $to = $employee->alt_email ?: $employee->email;

        if ($to) {
            try {
                Mail::to($to)->send(new SendCredentialsMail([
                    'email'    => $user->email,
                    'username' => $user->username,
                    'password' => $user->plain_password,
                ]));
            } catch (\Exception $e) {
                \Log::error('Failed to send credentials email: ' . $e->getMessage());
                // Don't fail the whole request if email fails
            }
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
        $employee          = Employee::findOrFail($id);
        $employee->archive = 'Archived';
        $employee->save();

        return response()->json(['status' => 'ok']);
    }

    public function unarchive($id)
    {
        $employee          = Employee::findOrFail($id);
        $employee->archive = null;
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

        $user     = User::where('username', $request->username)->firstOrFail();
        $employee = $user->employee;

        $plain = substr($employee->fname, 0, 3) . substr($employee->lname, 0, 3) . rand(100, 999);

        $user->password       = Hash::make($plain);
        $user->plain_password = $plain;
        $user->save();

        // ✅ Send to alt_email first, fallback to email
        $to = $employee->alt_email ?: $employee->email;

        if ($to) {
            try {
                Mail::to($to)->send(new SendCredentialsMail([
                    'email'    => $user->email,
                    'username' => $user->username,
                    'password' => $plain,
                ]));
            } catch (\Exception $e) {
                \Log::error('Failed to send reset password email: ' . $e->getMessage());
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Password reset but email failed: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ✅ ADDED: sendCredentials method (called from blade JS)
    public function sendCredentials(Request $request)
    {
        $request->validate([
            'to'       => 'required|email',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            Mail::to($request->to)->send(new SendCredentialsMail([
                'email'    => $request->username,
                'username' => $request->username,
                'password' => $request->password,
            ]));

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            \Log::error('sendCredentials failed: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}