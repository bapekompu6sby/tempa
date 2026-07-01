<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asn;

class AsnController extends Controller
{
    public function index()
    {
        $asns = Asn::paginate(20);
        return view('asn.index', compact('asns'));
    }

    public function create()
    {
        return view('asn.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:255|unique:asns,nip',
            'name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'birth_city' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:L,P',
            'rank_grade' => 'nullable|string|max:100',
            'latest_education' => 'nullable|string|max:100',
            'office_address' => 'nullable|string',
            'asn_type' => 'nullable|in:pns,cpns,pppk,lainnya',
            'asn_source' => 'nullable|in:pusat,daerah,lainnya',
        ]);

        Asn::create($validated);

        return redirect()->route('asn.index')->with('success', 'Data ASN berhasil ditambahkan.');
    }

    public function show(Asn $asn)
    {
        $asn->load('events');
        return view('asn.show', compact('asn'));
    }

    public function edit(Asn $asn)
    {
        return view('asn.edit', compact('asn'));
    }

    public function update(Request $request, Asn $asn)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:255|unique:asns,nip,' . $asn->id,
            'name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'birth_city' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:L,P',
            'rank_grade' => 'nullable|string|max:100',
            'latest_education' => 'nullable|string|max:100',
            'office_address' => 'nullable|string',
            'asn_type' => 'nullable|in:pns,cpns,pppk,lainnya',
            'asn_source' => 'nullable|in:pusat,daerah,lainnya',
        ]);

        $asn->update($validated);

        return redirect()->route('asn.index')->with('success', 'Data ASN berhasil diupdate.');
    }

    public function destroy(Asn $asn)
    {
        $asn->delete();
        return redirect()->route('asn.index')->with('success', 'Data ASN berhasil dihapus.');
    }
}
