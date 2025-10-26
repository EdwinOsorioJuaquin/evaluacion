<?php

namespace App\Http\Controllers\Auditoria;

use App\Models\CorrectiveAction;
use App\Models\Finding;
use App\Models\User;
use App\Models\Audit;
use App\Models\DocumentAudit;
use App\Models\DocumentVersion;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class CorrectiveActionController extends Controller
{
    
    protected $dates = ['engagement_date', 'completion_date'];
    
    // Mostrar todas las acciones correctivas de un hallazgo
    public function index(Audit $audit, Finding $finding)
    {
        $correctiveActions = $finding->correctiveActions; // Obtener todas las acciones correctivas relacionadas al hallazgo
        return view('auditoria.actions.index', compact('correctiveActions', 'audit', 'finding'));
    }

    public function create(Audit $audit, Finding $finding)
{
    return view('auditoria.actions.create', compact('audit', 'finding'));
}

public function store(Request $request, Audit $audit, Finding $finding)
{
    // Validación de los datos del formulario
    $request->validate([
        'description' => 'required|string',
        'engagement_date' => 'required|date',
        'due_date' => 'required|date',
    ]);

    // Crear la nueva acción correctiva
    $actionCorrective = new CorrectiveAction([
        'finding_id' => $finding->id,
        'user_id' => auth()->id(),
        'description' => $request->description,
        'status' => 'pending',
        'engagement_date' => $request->engagement_date,
        'due_date' => $request->due_date,
    ]);

    // Guardar la acción correctiva
    $actionCorrective->save();

    // Redirigir al usuario al detalle del hallazgo después de guardar la acción correctiva
    return redirect()->route('auditoria.findings.show', [$audit->id, $finding->id])->with('success', 'Acción Correctiva registrada exitosamente');
}


    // Método para mostrar detalles de una acción correctiva específica
    public function show(Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
    {
        $action = CorrectiveAction::with(['finding', 'user'])
        ->findOrFail($correctiveAction->id);

        return view('auditoria.actions.show', compact('correctiveAction', 'audit', 'finding', 'action'));
    }

    public function update(Request $request, Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed,cancelled',
    ]);

    // Actualizar el estado de la acción correctiva
    $correctiveAction->status = $request->status;

    // Si se marca como "completada", se agrega la fecha de ejecución
    if ($request->status == 'completed' && !$correctiveAction->completion_date) {
        $correctiveAction->completion_date = now();
    }

    $correctiveAction->save();

    // Si todas las acciones del hallazgo están completadas, actualizar el hallazgo a "Revisado"
    $allCompleted = $finding->correctiveActions->every(fn($action) => $action->status == 'completed');
    if ($allCompleted) {
        $finding->classification = 'Revisado';
        $finding->save();
    }

    return redirect()->route('auditoria.actions.show', [$audit->id, $finding->id, $correctiveAction->id])
        ->with('success', 'Estado de la acción correctiva actualizado.');
}


    public function updateDates(Request $request, Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    $request->validate([
        'engagement_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:engagement_date',
    ]);

    $correctiveAction->engagement_date = $request->engagement_date;
    $correctiveAction->due_date = $request->due_date;
    $correctiveAction->save();

    return redirect()->route('auditoria.actions.show', [$audit->id, $finding->id, $correctiveAction->id])
        ->with('success', 'Fechas actualizadas correctamente.');
}

public function start(Request $request, Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    // Cambiar el estado a 'in_progress' cuando se inicia la acción correctiva
    $correctiveAction->status = 'in_progress';
    $correctiveAction->save();

    return redirect()->route('auditoria.actions.show', [$audit->id, $finding->id, $correctiveAction->id])
        ->with('success', 'Acción Correctiva iniciada');
}

public function updateExecutionDate(Request $request, Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    $request->validate([
        'completion_date' => 'required|date',
    ]);

    // Actualizar la fecha de ejecución y marcar la acción como completada
    $correctiveAction->completion_date = $request->completion_date;
    $correctiveAction->status = 'completed';
    $correctiveAction->save();

    // Actualizar el hallazgo a 'Revisado' si todas las acciones están completadas
    if ($finding->correctiveActions->every(fn($correctiveAction) => $correctiveAction->status == 'completed')) {
        $finding->classification = 'Revisado';
        $finding->save();
    }

    return redirect()->route('auditoria.actions.show', [$audit->id, $finding->id, $correctiveAction->id])
        ->with('success', 'Acción Correctiva completada');
}



// Método general para actualizar el estado y la fecha de ejecución
public function updateStatus(Request $request, Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'completion_date' => 'nullable|date|before_or_equal:today', // Validar la fecha de ejecución
    ]);

    // Iniciar una transacción para asegurar la integridad de los datos
    DB::beginTransaction();
    try {
        // Actualizar el estado
        $correctiveAction->status = $request->status;

        // Si la acción está "completada", agregar la fecha real de ejecución
        if ($request->status == 'completed') {
            $correctiveAction->completion_date = $request->completion_date ?: now();
        }

        $correctiveAction->save();

        // Si todas las acciones correctivas de un hallazgo están completas, actualizar el hallazgo a "Revisado"
        if ($finding->correctiveActions->every(fn($correctiveAction) => $correctiveAction->status == 'completed')) {
            $finding->classification = 'Revisado';
            $finding->save();
        }

        // Commit de la transacción
        DB::commit();

        return redirect()->route('auditoria.actions.show', [$audit->id, $finding->id, $correctiveAction->id])
            ->with('success', 'Estado de la acción correctiva actualizado.');
    } catch (\Exception $e) {
        // Rollback de la transacción en caso de error
        DB::rollback();
        return back()->withErrors(['error' => 'Hubo un error al actualizar el estado de la acción correctiva.']);
    }
}



public function updateCompletionDate(Request $request, Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    $request->validate([
        'completion_date' => 'required|date|before_or_equal:today',
    ]);

    // Actualizar la fecha de completado
    $correctiveAction->completion_date = $request->completion_date;
    $correctiveAction->status = 'completed'; // Cambiar estado a completado
    $correctiveAction->save();

    // Actualizar el estado del hallazgo a "Revisado" si todas las acciones están completadas
    if ($finding->correctiveActions->every(fn($correctiveAction) => $correctiveAction->status == 'completed')) {
        $finding->classification = 'Revisado';
        $finding->save();
    }

    return redirect()->route('auditoria.actions.show', ['audit' => $audit->id, 'finding' => $finding->id, 'correctiveAction' => $correctiveAction->id])
        ->with('success', 'Acción Correctiva Completada');
}


// app/Http/Controllers/Auditoria/ActionController.php

public function edit(Audit $audit, Finding $finding, CorrectiveAction $correctiveAction)
{
    $usuarios = User::whereJsonContains('role', 'auditor')
                ->orWhereJsonContains('role', 'admin')
                ->get();

    return view('auditoria.actions.edit', [
        'audit' => $audit,
        'finding' => $finding,
        'correctiveAction' => $correctiveAction,
        'usuarios' => $usuarios,
    ]);
}


}