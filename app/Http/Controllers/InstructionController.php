<?php

namespace App\Http\Controllers;

use App\Models\Instruction;
use Illuminate\Http\Request;

class InstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $instructions = Instruction::all();
    return view('instructions.index', compact('instructions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    return view('instructions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
        ]);
        $instruction = Instruction::create($validated);
        return redirect()->route('instructions.index')->with('success', 'Instruksi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Instruction $instruction)
    {
    return view('instructions.show', compact('instruction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instruction $instruction)
    {
    return view('instructions.edit', compact('instruction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instruction $instruction)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
        ]);
        $instruction->update($validated);
        return redirect()->route('instructions.index')->with('success', 'Instruksi berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instruction $instruction)
    {
    $instruction->delete();
    return redirect()->route('instructions.index')->with('success', 'Instruksi berhasil dihapus');
    }
}
