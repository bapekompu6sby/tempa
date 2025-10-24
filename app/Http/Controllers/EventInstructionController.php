<?php

namespace App\Http\Controllers;

use App\Models\EventInstruction;
use Illuminate\Http\Request;

class EventInstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EventInstruction $eventInstruction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventInstruction $eventInstruction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventInstruction $eventInstruction)
    {
        // Update generic fields if needed (not used for toggle endpoint)
        $data = $request->validate([
            'checked' => 'nullable|boolean',
            'link' => 'nullable|string',
        ]);
        $eventInstruction->fill($data);
        $eventInstruction->save();
        return response()->json(['success' => true, 'eventInstruction' => $eventInstruction]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventInstruction $eventInstruction)
    {
        // delete
        $eventInstruction->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Toggle the checked boolean for an EventInstruction.
     */
    public function toggle(EventInstruction $eventInstruction)
    {
        $eventInstruction->checked = !$eventInstruction->checked;
        $eventInstruction->save();

        return response()->json([
            'checked' => (bool) $eventInstruction->checked,
            'id' => $eventInstruction->id,
        ]);
    }
}
