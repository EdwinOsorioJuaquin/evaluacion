<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\AuditReport;
use App\Models\DocumentVersion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AuditReportController extends Controller
{
    /**
     * Mostrar formulario para crear un nuevo informe de auditoría.
     */
    public function create(Audit $audit)
    {
        return view('auditoria.auditReports.create', compact('audit'));
    }

    /**
     * Guardar las recomendaciones e informe final de la auditoría.
     */
    public function store(Request $request, Audit $audit)
    {
        $validated = $request->validate([
            'resume'           => 'required|string|max:2000',
            'recommendations'  => 'required|string|max:5000',
            'indicators'       => 'nullable|string|max:2000',
        ]);

        // Crear versión documental (opcional)
        $documentVersion = DocumentVersion::create([
            'document_id'   => $audit->id,
            'file_name'     => 'Informe_Auditoria_' . $audit->id . '.pdf',
            'storage_path'  => 'auditoria/reports/informe_' . $audit->id . '.pdf',
        ]);

        // Registrar informe
        $report = new AuditReport([
            'audit_id'             => $audit->id,
            'version_document_id'  => $documentVersion->id,
            'resume'               => $validated['resume'],
            'recommendations'      => $validated['recommendations'],
            'indicators'           => $validated['indicators'] ?? null,
            'generation_date'      => now(),
        ]);
        $report->save();

        return redirect()
            ->route('auditoria.audits.show', $audit->id)
            ->with('success', '✅ Informe de auditoría creado correctamente.');
    }

    /**
     * Previsualizar el informe de auditoría antes de generar el PDF.
     */
    public function preview(Audit $audit)
    {
        $report = $audit->auditReports()->latest()->first();
        if (!$report) {
            return redirect()
                ->route('auditoria.audits.show', $audit->id)
                ->with('error', 'No hay informe disponible para previsualizar.');
        }

        return view('auditoria.auditReports.preview', compact('audit', 'report'));
    }

    /**
     * Generar y descargar el PDF del informe de auditoría.
     */
    public function pdf(Audit $audit)
    {
        $report = $audit->auditReports()->latest()->first();

        if (!$report) {
            return redirect()
                ->route('auditoria.audits.show', $audit->id)
                ->with('error', 'No hay informe registrado para generar PDF.');
        }

        $pdf = Pdf::loadView('auditoria.auditReports.pdf', compact('audit', 'report'))
                  ->setPaper('a4', 'portrait');

        $filePath = 'auditoria/reports/informe_' . $audit->id . '.pdf';
        Storage::disk('public')->put($filePath, $pdf->output());

        return $pdf->download('Informe_Auditoria_' . $audit->id . '.pdf');
    }

    /**
     * Descargar el PDF previamente generado.
     */
    public function download(Audit $audit)
    {
        $report = $audit->auditReports()->latest()->first();
        if (!$report) {
            return redirect()
                ->route('auditoria.audits.show', $audit->id)
                ->with('error', 'No hay informe disponible para descargar.');
        }

        $path = 'public/auditoria/reports/informe_' . $audit->id . '.pdf';
        if (!Storage::exists($path)) {
            return redirect()
                ->route('auditoria.audits.show', $audit->id)
                ->with('error', 'El archivo PDF no se encuentra en el sistema.');
        }

        return Storage::download($path);
    }

    /**
     * Marcar la auditoría como completada (opcional).
     */
    public function complete(Audit $audit)
    {
        $audit->update(['state' => 'completed']);

        return redirect()
            ->route('auditoria.audits.index')
            ->with('success', 'La auditoría fue marcada como completada.');
    }
}
