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
    // prepare collections for tabs with optional search, phase and learning-model filters
    $tab = request('tab', 'semua');
    $q = request('q');
    // phase filter: 'all' means no filtering
    $phase = request('phase', 'all');

    // collect learning model checkbox filters
    $filters = request()->only(['full_elearning', 'distance_learning', 'blended_learning', 'classical']);
    // normalize to boolean presence
    $selected = array_filter($filters);

    $build = function ($role = null) use ($q, $selected, $phase) {
        $query = Instruction::query();
        if ($role) {
            $query->where('role', $role);
        }
        // apply phase filter when a specific phase is selected
        if (!empty($phase) && $phase !== 'all') {
            $query->where('phase', $phase);
        }
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('detail', 'like', "%{$q}%");
            });
        }

        // if any learning-model filter is selected, include instructions that match ANY of them
        if (!empty($selected)) {
            $query->where(function ($q2) use ($selected) {
                foreach ($selected as $col => $val) {
                    $q2->orWhere($col, true);
                }
            });
        }

        // enforce specific phase ordering: persiapan -> pembukaan_pelatihan -> pelaksanaan -> penutupan_pelatihan -> evaluasi_pelatihan -> pasca_pelatihan
        $orderSql = "CASE phase
            WHEN 'persiapan' THEN 1
            WHEN 'pembukaan_pelatihan' THEN 2
            WHEN 'pelaksanaan' THEN 3
            WHEN 'penutupan_pelatihan' THEN 4
            WHEN 'evaluasi_pelatihan' THEN 5
            WHEN 'pasca_pelatihan' THEN 6
            ELSE 7 END";

        return $query->orderByRaw($orderSql)->orderBy('id')->get();
    };


    $all = $build();
    $pic = $build('pic');
    $host = $build('host');
    $pengamat = $build('petugas_kelas');

    return view('instructions.index', compact('all', 'pic', 'host', 'pengamat', 'tab', 'q', 'filters', 'phase'));
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
            'role' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
        ]);
        // checkbox fields: present when checked; default false
        $validated['linkable'] = $request->has('linkable');
        $validated['full_elearning'] = $request->has('full_elearning');
        $validated['distance_learning'] = $request->has('distance_learning');
        $validated['blended_learning'] = $request->has('blended_learning');
        $validated['classical'] = $request->has('classical');
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
            'role' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
        ]);
        $validated['linkable'] = $request->has('linkable');
        $validated['full_elearning'] = $request->has('full_elearning');
        $validated['distance_learning'] = $request->has('distance_learning');
        $validated['blended_learning'] = $request->has('blended_learning');
        $validated['classical'] = $request->has('classical');
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
