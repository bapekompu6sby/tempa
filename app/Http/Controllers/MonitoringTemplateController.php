<?php

namespace App\Http\Controllers;

use App\Models\MonitoringTemplate;
use Illuminate\Http\Request;

class MonitoringTemplateController extends Controller
{
    public function index()
    {
        $templates = MonitoringTemplate::orderBy('name')->paginate(10);
        return view('monitoring_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('monitoring_templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:sikap,sarpras,administrasi,administrasi_peserta',
            'sub_category' => 'nullable|string|max:255',
            'ownership' => 'required|in:event,event_subject',
        ]);

        MonitoringTemplate::create($validated);

        return redirect()->route('monitoring-templates.index')
            ->with('success', 'Monitoring Template created successfully.');
    }

    public function edit(MonitoringTemplate $monitoringTemplate)
    {
        return view('monitoring_templates.edit', compact('monitoringTemplate'));
    }

    public function update(Request $request, MonitoringTemplate $monitoringTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:sikap,sarpras,administrasi,administrasi_peserta',
            'sub_category' => 'nullable|string|max:255',
            'ownership' => 'required|in:event,event_subject',
        ]);

        $monitoringTemplate->update($validated);

        return redirect()->route('monitoring-templates.index')
            ->with('success', 'Monitoring Template updated successfully.');
    }

    public function destroy(MonitoringTemplate $monitoringTemplate)
    {
        $monitoringTemplate->delete();

        return redirect()->route('monitoring-templates.index')
            ->with('success', 'Monitoring Template deleted successfully.');
    }
}
