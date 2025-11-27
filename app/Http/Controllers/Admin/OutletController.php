<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class OutletController extends Controller
{
    /**
     * Display a listing of the outlets.
     */
    public function index()
    {

        return view('admin.outlets.index');
    }

    /**
     * Show the form for creating a new outlet.
     */
    public function create()
    {
        $outletManagers = User::where('role', 'outlet-manager')->get();
        return view('admin.outlets.create', compact('outletManagers'));
    }

    /**
     * Store a newly created outlet in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'address' => 'required|string',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Outlet manager is required , please select',
            'name.unique' => 'This outlet name already exists.',
        ]);


        Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'status' => $request->status,
            'type' => 'outlet',
            'user_id' => $request->user_id,
        ]);

        $user = User::find($request->user_id);
        $outlet_username = $user->name;
        $email = $user->email;
        $mailData = attachEmailTemplate('add-outlet', [
            'name' => ucfirst($outlet_username),
            'outletname' => ucfirst($request->name),
            'email' => $email,

        ]);

        sendEmail($mailData['body'], $mailData['subject'], $email);

        return redirect()->route('admin.outlets.index')->with('success', 'Outlet created successfully.');
    }


    /**
     * Show the form for editing the specified outlet.
     */
    public function edit($id)
    {
        $outlet = Location::findOrFail($id);
        $outletManagers = User::where('role', 'outlet-manager')->get();

        return view('admin.outlets.edit', compact('outlet', 'outletManagers'));
    }


    public function show($id)
    {
        $outlet = Location::where('id', $id)
            // ->where('type', 'outlet')
            ->firstOrFail();
        return view('admin.outlets.show', compact('outlet'));
    }
    /**
     * Update the specified outlet in storage.
     */
    public function update(Request $request, $id)
    {
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'address' => 'required|string',
        //     'status' => 'nullable|boolean',
        //     'description' => 'nullable|string',

        // ]);

        // $validated['status'] = $request->has('status') ? $request->status : 1;

        // $outlet->update($validated);

        $request->validate([
            // 'name' => 'required|string|max:255',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'name')->ignore($id),
            ],
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'user_id' => 'required|exists:users,id',

        ], [
            'name.unique' => 'This outlet name already exists.',
        ]);

        $outlet = Location::findOrFail($id);
        $outlet->update([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'status' => $request->status,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('admin.outlets.index')->with('success', 'Outlet updated successfully.');
    }

    /**
     * Remove the specified outlet from storage.
     */
    // public function destroy($id)
    // {
    //     $outlet = Outlet::findOrFail($id);
    //     $outlet->delete();

    //     return redirect()->route('admin.outlets.index')->with('success', 'Outlet deleted successfully.');
    // }

}
