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
}
