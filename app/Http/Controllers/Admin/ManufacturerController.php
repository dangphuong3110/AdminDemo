<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manufacturers = Manufacturer::latest()->paginate(5);

        return view('admin.manufacturer.index', compact('manufacturers'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.manufacturer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name-manufacturer' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('manufacturers.create')->withErrors($validator)->withInput();
        }

        $manufacturer = new Manufacturer();
        $manufacturer->name = $request->input('name-manufacturer');

        $manufacturer->save();

        return redirect()->route('manufacturers.index')->with('success', 'Manufacturer has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        return view('admin.manufacturer.edit', compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name-manufacturer' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('manufacturers.create')->withErrors($validator)->withInput();
        }

        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->name = $request->input('name-manufacturer');

        $manufacturer->save();

        return redirect()->route('manufacturers.index')->with('success', 'Manufacturer has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        $manufacturer->delete();

        return redirect()->route('manufacturers.index')->with('success', 'Manufacturer deleted successfully.');
    }
}
