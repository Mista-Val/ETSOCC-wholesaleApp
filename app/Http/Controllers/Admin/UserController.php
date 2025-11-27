<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Livewire\Component;
use Livewire\WithPagination;

class UserController extends Controller
{

    public function index(Request $request)
    {
        return view('admin.users.index');
    }

    public function create()
    {
        $permissions = DB::table('permissions')->get();
        $roles = config('global.roles');
        return view('admin.users.create', compact('permissions', 'roles'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
            'status'   => 'required|boolean',
            'role'     => 'required|string',
            'permissions' => 'required|array',
            'permissions.*' => 'required|integer|exists:permissions,id',
        ], [
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password and Confirm Password do not match.',
            'password_confirmation.required' => 'Please confirm your password.',
            'password_confirmation.min' => 'Confirm Password must be at least 8 characters.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.integer' => 'Invalid permission selected.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
        ]);

        $fullName = trim($request->fullname);
        $nameParts = explode(' ', $fullName, 2);

        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $user = User::create([
            'name' => $request->fullname,
            'first_name' => ucfirst($firstName),
            'last_name'  => ucfirst($lastName),
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'status'     => $request->status,
            'role'       => $request->role,
        ]);

        $permissionData = [];
        foreach ($request->permissions as $permissionId) {
            $permissionData[] = [
                'permission_id' => $permissionId,
                'model_type'    => User::class,
                'model_id'      => $user->id,
            ];
        }

        DB::table('model_has_permissions')->insert($permissionData);


        $mailData = attachEmailTemplate('add-user', [
            'name' => ucfirst($request->fullname),
            'email' => $request->email,
            'password' => $request->password
        ]);

        sendEmail($mailData['body'], $mailData['subject'], $request->email);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $permissions = DB::table('permissions')->get();
        $roles = config('global.roles');

        // Get user's current permissions
        $userPermissions = DB::table('model_has_permissions')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->pluck('permission_id')
            ->toArray();

        return view('admin.users.edit', compact('user', 'permissions', 'roles', 'userPermissions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // $request->validate([
        //     'fullname' => 'required|string|max:255',
        //     'email'    => 'required|email|unique:users,email,' . $user->id,
        //     'status'   => 'required|boolean',
        //     'role'     => 'required|string',
        //     'permissions' => 'required|array',
        //     'permissions.*' => 'required|integer|exists:permissions,id',
        //     'password' => 'nullable|min:8|confirmed',
        // ]);

        $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'status'   => 'required|boolean',
            'role'     => 'required|string',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'integer|exists:permissions,id',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'permissions.required' => 'Please select at least one permission.',
            'permissions.min' => 'Please select at least one permission.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.integer' => 'Invalid permission selected.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password and Confirm Password do not match.',
        ]);

        $names = explode(' ', $request->fullname, 2);
        $firstName = $names[0];
        $lastName = $names[1] ?? '';

        // Update user fields
        $user->name = $request->fullname;
        $user->first_name = ucfirst($firstName);
        $user->last_name = ucfirst($lastName);
        $user->email = $request->email;
        $user->status = $request->status;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update permissions
        // Remove old permissions
        DB::table('model_has_permissions')->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->delete();

        // Insert new permissions
        $permissionData = [];
        foreach ($request->permissions as $permissionId) {
            $permissionData[] = [
                'permission_id' => $permissionId,
                'model_type'    => User::class,
                'model_id'      => $user->id,
            ];
        }
        DB::table('model_has_permissions')->insert($permissionData);

        // Optional: send email if password was updated
        // if ($request->filled('password')) {
        //     $mailData = attachEmailTemplate('update-user', [
        //         'name' => ucfirst($request->fullname),
        //         'email' => $request->email,
        //         'password' => $request->password
        //     ]);

        //     sendEmail($mailData['body'], $mailData['subject'], $request->email);
        // }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
}
