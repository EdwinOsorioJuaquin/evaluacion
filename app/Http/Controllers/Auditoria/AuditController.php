<?php

namespace App\Http\Controllers\Auditoria;

use App\Models\Audit;
use App\Models\CorrectiveAction;
use App\Models\AuditReport;
use App\Models\DocumentAudit;
use App\Models\DocumentVersion;
use App\Models\Document;
use App\Models\User;

use App\Models\Finding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class AuditController extends Controller
{
     // Listado (admin: todas; auditor: asignadas a él)
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Audit::query();

        // Soft-delete por estado 'cancelled'
        ($request->get('show') === 'deleted') ? $query->onlyDeleted() : $query->visible();

        // Rol: limitar para auditor
        if ($user->hasRole('auditor') && !$user->hasRole('admin')) {
            $query->where('assigned_user_id', $user->id);
        }

        // Filtro por estado
        if ($state = $request->get('state')) {
            $query->where('state', $state);
        }

        // Búsqueda simple
        if ($q = trim($request->get('q', ''))) {
            $query->where(function($w) use ($q) {
                $w->where('objective', 'like', "%{$q}%")
                  ->orWhere('area', 'like', "%{$q}%");
            });
        }

        $audits = $query->latest('start_date')->paginate(12);
        return view('auditoria.audits.index', compact('audits'));
    }   


    // Ver detalles de una Auditoría
    public function show(Audit $audit)
    {
        // (opcional) restringir a assigned_user_id si es auditor
        $user = Auth::user();
        if ($user->hasRole('auditor') && !$user->hasRole('admin') && $audit->assigned_user_id !== $user->id) {
            abort(403);
        }

        $findings = $audit->findings;
        return view('auditoria.audits.show', compact('audit', 'findings'));
    }

     // Editar (admin)
    public function edit(Audit $audit)
    {
        $auditors = [];
        if (auth()->user()?->hasRole('admin')) {
            $auditors = User::whereJsonContains('role','auditor')
                ->orderByRaw("COALESCE(NULLIF(TRIM(CONCAT(first_name,' ',last_name)),''), full_name, email)")
                ->get(['id','first_name','last_name','full_name','email']);
        }
        return view('auditoria.audits.edit', compact('audit','auditors'));
    }

    // Actualizar (admin)
    public function update(Request $request, Audit $audit)
    {
        $request->validate([
            'area'               => 'required|string|max:255',
            'objective'          => 'required|string|max:255',
            'type'               => 'required|in:internal,external',
            'state'              => 'required|in:planned,in_progress,completed,cancelled',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'assigned_user_id'   => 'nullable|exists:users,id',
            'summary_results'    => 'nullable|string',
        ]);

        $audit->update([
            'area'             => $request->area,
            'objective'        => $request->objective,
            'type'             => $request->type,
            'state'            => $request->state,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'assigned_user_id' => $request->assigned_user_id ?: $audit->user_id,
            'summary_results'  => $request->summary_results,
        ]);

        return redirect()->route('auditoria.audits.show', $audit)->with('success', 'Auditoría actualizada');
    }

    // Mostrar los hallazgos de una auditoría
    public function findings(Audit $audit)
    {
        $findings = $audit->findings; // Obtener los hallazgos asociados
        return view('findings.index', compact('findings', 'audit'));
    }
 // Añadir hallazgo a la auditoría
    public function storeFinding(Request $request, Audit $audit)
    {
        $request->validate([
            'finding_description' => 'required|string|max:255',
            'evidence' => 'nullable|string|max:255',
        ]);

        $finding = Finding::create([
            'audit_id' => $audit->id,
            'description' => $request->finding_description,
            'classification' => 'Observado', // Estado inicial "Observado"
            'evidence' => $request->evidence,
            'severity' => 'medium',  // Valor por defecto
            'discovery_date' => now(),
        ]);

        // Crear reporte inicial del hallazgo
        AuditReport::create([
            'audit_id' => $audit->id,
            'resume' => $finding->description,
            'recommendations' => 'Ninguna', // Recomendaciones iniciales
            'indicators' => 'Ninguno', // Indicadores iniciales
            'generation_date' => now(),
        ]);

        return redirect()->route('auditoria.audits.show', $audit->id)->with('success', 'Hallazgo registrado exitosamente');
    }

    // Método para iniciar la auditoría (cambiar el estado a "in_progress")
    public function start(Audit $audit)
    {
        $audit->update(['state' => 'in_progress']);
        return redirect()->route('auditoria.audits.show', $audit->id);
    }


     // Completar auditoría (cambiar el estado a "completed")
    public function completeAudit(Audit $audit)
    {
        // Verificar que todos los hallazgos estén revisados
        $allFindingsReviewed = $audit->findings->every(function ($finding) {
            return $finding->classification == 'Revisado';
        });

        if ($allFindingsReviewed) {
            $audit->update(['state' => 'completed']);
            return redirect()->route('auditoria.audits.show', $audit->id)->with('success', 'Auditoría completada');
        }

        return redirect()->route('auditoria.audits.show', $audit->id)->with('error', 'No se puede completar la auditoría mientras haya hallazgos sin revisar');
    }

    public function storeCorrectiveAction(Request $request, Audit $audit, Finding $finding)
{
    // Validar los datos
    $validated = $request->validate([
        'description' => 'required|string|max:255',
        'due_date' => 'required|date|after_or_equal:today', // La fecha de vencimiento debe ser posterior a hoy
    ]);

    // Crear la acción correctiva
    $correctiveAction = new CorrectiveAction();
    $correctiveAction->finding_id = $finding->id;
    $correctiveAction->user_id = auth()->id(); // El usuario que está registrando la acción
    $correctiveAction->description = $validated['description'];
    $correctiveAction->due_date = $validated['due_date'];
    $correctiveAction->status = 'pending'; // Inicialmente, el estado será "pendiente"
    $correctiveAction->save();

    // Actualizar el hallazgo a "Observado"
    $finding->classification = 'Observado';
    $finding->save();

    return redirect()->route('auditoria.audits.show', $audit->id)->with('success', 'Acción correctiva registrada exitosamente');
}


public function resolveCorrectiveAction(Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    // Cambiar el estado de la acción correctiva a 'completado'
    $correctiveAction->status = 'completed';
    $correctiveAction->completion_date = now(); // Fecha de resolución
    $correctiveAction->save();

    // Actualizar el hallazgo a 'Revisado'
    $finding->classification = 'Revisado';
    $finding->save();

    return redirect()->route('auditoria.audits.show', $audit->id)->with('success', 'Acción correctiva completada y hallazgo revisado');
}

public function generateAuditReport(Audit $audit)
{
    $findings = $audit->findings()->with('correctiveActions')->get();
    $pdf = PDF::loadView('auditoria.audits.report', compact('audit', 'findings'));
    
    return $pdf->download('reporte_auditoria_' . $audit->id . '.pdf');
}

/**
     * Cambiar el estado de la auditoría a completada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Audit  $audit
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request, Audit $audit)
    {
        // Verifica si la auditoría ya está en progreso
        if ($audit->state != 'in_progress') {
            return redirect()->route('auditoria.audits.show', $audit->id)->with('error', 'La auditoría no está en progreso.');
        }

        // Actualizar el estado de la auditoría a 'completada'
        $audit->state = 'completed';
        $audit->save();

        return redirect()->route('auditoria.audits.show', $audit->id)->with('success', 'La auditoría ha sido completada.');
    }


 // AuditController.php

public function storeRecommendations(Request $request, $auditId)
{
    // Validamos los datos que se están enviando.
    $request->validate([
        'resume' => 'required|string|max:255',
        'recommendations' => 'required|string|max:255',
        'indicators' => 'nullable|string|max:255',
    ]);

    // Obtenemos la auditoría a la que pertenecen las recomendaciones
    $audit = Audit::findOrFail($auditId);

    // Creamos el reporte de auditoría con las recomendaciones
    $auditReport = new AuditReport([
        'audit_id' => $audit->id,
        'resume' => $request->resume,
        'recommendations' => $request->recommendations,
        'indicators' => $request->indicators,
        'generation_date' => now(),
    ]);

    $auditReport->save();

    // Relacionamos el reporte con la auditoría, actualizando el campo report_document_version_id en la auditoría
    $audit->update([
        'report_document_version_id' => $auditReport->id,
    ]);

    // Redirigimos al detalle de la auditoría después de guardar las recomendaciones
    return redirect()->route('auditoria.audits.show', $audit->id)->with('success', 'Recomendaciones registradas exitosamente');
}


public function generateReportPDF($auditId)
{
    // Obtener la auditoría con sus reportes, hallazgos y acciones correctivas
    $audit = Audit::with(['auditReports', 'findings.correctiveActions'])->findOrFail($auditId);
    $report = $audit->auditReports()->latest()->first(); // Obtener el último reporte generado.
    
    // Agregar detalles de hallazgos y acciones correctivas a la vista
    $findings = $audit->findings; // Todos los hallazgos de la auditoría

    $logoPath = public_path('images/incadev-logo.png'); // Ruta al logo de la empresa
    // Convertimos el logo a Base64 para que se muestre dentro del PDF
        $logo = base64_encode(file_get_contents($logoPath));


    // Generar el PDF usando la vista 'auditoria.audits.pdf'
    $pdf = \PDF::loadView('auditoria.audits.pdf', compact('audit', 'report', 'findings', 'logo'))
         ->setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true])
         ->setPaper('A4', 'portrait');
    // Guardar el archivo PDF
    $filePath = 'audits/reports/audit_report_' . $audit->id . '.pdf';
    Storage::disk('public')->put($filePath, $pdf->output());

    // Crear un nuevo documento para el reporte
    $document = Document::create([
        'title' => 'Reporte de Auditoría - ' . $audit->id,
        'category' => 'administrative',  // Puedes cambiar la categoría si es necesario
        'entity_type' => 'audit',
        'entity_id' => $audit->id,
        'status' => 'active',  // O lo que sea adecuado
        'file_path' => $filePath,
        'created_by' => auth()->id(),
    ]);

    // Crear una nueva versión del documento
    $documentVersion = DocumentVersion::create([
        'document_id' => $document->id,
        'version_number' => $document->documentVersions()->count() + 1,
        'file_name' => 'Reporte de Auditoría - ' . $audit->id,
        'storage_path' => $filePath,
        'mime_type' => 'application/pdf',
        'file_size' => Storage::disk('public')->size($filePath),
        'uploaded_by_user_id' => auth()->id(),
    ]);

    // Relacionar el documento con la auditoría
    DocumentAudit::create([
        'audit_id' => $audit->id,
        'version_document_id' => $documentVersion->id,
    ]);

    // Actualizar el campo report_document_version_id en la auditoría
    $audit->update([
        'report_document_version_id' => $documentVersion->id,
    ]);

    


    // Retornar el archivo PDF o redirigir según lo que se desee
    return $pdf->stream('audit_report_' . $audit->id . '.pdf');
}
    // Eliminar una auditoría (solo admin)
    public function destroy(Audit $audit)
    {
        $audit->state = 'cancelled';
        $audit->save();

        return redirect()->route('auditoria.dashboard.index')->with('success', 'Auditoría eliminada exitosamente');
    }



}
