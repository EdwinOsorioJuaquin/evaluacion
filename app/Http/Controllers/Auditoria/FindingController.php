<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\Finding;
use App\Models\CorrectiveAction;
use Illuminate\Support\Facades\Storage;

class FindingController extends Controller
{
    /**
     * Mostrar los hallazgos de una auditoría.
     */
    public function index(Audit $audit)
    {
        $findings = $audit->findings();
        return view('auditoria.findings.index', compact('audit', 'findings'));
    }

    /**
     * Ver detalle de un hallazgo.
     */
    public function show(Audit $audit, Finding $finding)
    {
        $correctiveActions = $finding->correctiveActions ?? collect();
        return view('auditoria.findings.show', compact('audit', 'finding', 'correctiveActions'));
    }

    /**
     * Guardar un nuevo hallazgo.
     */
    public function store(Request $request, Audit $audit)
    {
        $validated = $request->validate([
            'description'    => 'required|string|max:2000',
            'classification' => 'required|string|in:Revisado,Observado,No aplica',
            'severity'       => 'required|string|in:high,medium,low',
            'evidence'       => 'nullable|string|max:5000',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $finding = new Finding([
            'audit_id'      => $audit->id,
            'description'   => $validated['description'],
            'classification'=> $validated['classification'],
            'severity'      => $validated['severity'],
            'discovery_date'=> now(),
        ]);

        // Guardar evidencia (texto o archivo)
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('auditoria/findings', 'public');
            $finding->evidence = $path;
        } elseif (!empty($validated['evidence'])) {
            $finding->evidence = $validated['evidence'];
        }

        $finding->save();

        return redirect()
            ->route('auditoria.audits.show', $audit->id)
            ->with('success', '✅ Hallazgo registrado correctamente.');
    }

    /**
     * Mostrar formulario de edición de un hallazgo.
     */
    public function edit(Audit $audit, Finding $finding)
    {
        return view('auditoria.findings.edit', compact('audit', 'finding'));
    }

    /**
     * Actualizar un hallazgo.
     */
    public function update(Request $request, Audit $audit, Finding $finding)
    {
        $validated = $request->validate([
            'description'    => 'required|string|max:2000',
            'classification' => 'required|string|in:Revisado,Observado,No aplica',
            'severity'       => 'required|string|in:high,medium,low',
            'evidence'       => 'nullable|string|max:5000',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $finding->description    = $validated['description'];
        $finding->classification = $validated['classification'];
        $finding->severity       = $validated['severity'];

        if ($request->hasFile('document')) {
            // Eliminar archivo anterior si existe
            if ($finding->evidence && Storage::disk('public')->exists($finding->evidence)) {
                Storage::disk('public')->delete($finding->evidence);
            }

            $path = $request->file('document')->store('auditoria/findings', 'public');
            $finding->evidence = $path;
        } elseif (!empty($validated['evidence'])) {
            $finding->evidence = $validated['evidence'];
        }

        $finding->save();

        return redirect()
            ->route('auditoria.findings.index', $audit->id)
            ->with('success', '✅ Hallazgo actualizado correctamente.');
    }

    /**
     * Eliminar (soft-delete) un hallazgo.
     */
    public function destroy(Audit $audit, Finding $finding)
    {
        $finding->delete();

        return redirect()
            ->route('auditoria.findings.index', $audit->id)
            ->with('success', '🗑️ Hallazgo eliminado correctamente.');
    }
}
