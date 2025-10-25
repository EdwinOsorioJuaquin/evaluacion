<?php

namespace App\Http\Controllers\Auditoria;

use App\Models\User;
use App\Models\Audit;
use App\Models\Finding;
use App\Models\CorrectiveAction;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin   = $user->hasRole('admin');
        $isAuditor = $user->hasRole('auditor');

        // Base: auditor ve solo asignadas; admin ve todo
        $auditsBase = Audit::visible();
        if ($isAuditor && !$isAdmin) {
            $auditsBase->where('assigned_user_id', $user->id);
        }

        // IDs para filtros derivados
        $auditIds = (clone $auditsBase)->pluck('id');

        // ===== KPIs =====
        $totalAudits     = (clone $auditsBase)->count();
        $activeAudits    = (clone $auditsBase)->where('state', 'in_progress')->count();
        $completedAudits = (clone $auditsBase)->where('state', 'completed')->count();
        $plannedAudits   = (clone $auditsBase)->where('state', 'planned')->count();
        $cancelledAudits = (clone $auditsBase)->where('state', 'cancelled')->count();

        $totalFindings   = Finding::whereIn('audit_id', $auditIds)->count();
        $openFindings    = Finding::whereIn('audit_id', $auditIds)->where('classification', 'Observado')->count();
        $reviewedFinds   = Finding::whereIn('audit_id', $auditIds)->where('classification', 'Revisado')->count();

        $actionsBase     = CorrectiveAction::whereHas('finding', fn($q) => $q->whereIn('audit_id', $auditIds));
        $actionsPending  = (clone $actionsBase)->where('status', 'pending')->count();
        $actionsInProg   = (clone $actionsBase)->where('status', 'in_progress')->count();
        $actionsDone     = (clone $actionsBase)->where('status', 'completed')->count();
        $actionsOverdue  = (clone $actionsBase)
                            ->whereIn('status', ['pending','in_progress'])
                            ->whereDate('due_date', '<', now()->toDateString())
                            ->count();

        // ===== Auditorías por mes (últimos 12) =====
        $labelsMonths = [];
        $dataMonths   = [];
        for ($i = 11; $i >= 0; $i--) {
            $from = Carbon::now()->startOfMonth()->subMonths($i);
            $to   = (clone $from)->endOfMonth();
            $labelsMonths[] = $from->isoFormat('MMM YYYY'); // Ej: "ene 2025"
            $dataMonths[]   = (clone $auditsBase)
                ->whereBetween('start_date', [$from->toDateString(), $to->toDateString()])
                ->count();
        }

        // ===== Distribución por estado (para la dona) =====
        $stateCounts = [
            'planned'     => $plannedAudits,
            'in_progress' => $activeAudits,
            'completed'   => $completedAudits,
            'cancelled'   => $cancelledAudits,
        ];

        // ===== Top 10 auditorías con más hallazgos =====
        $topFindings = Finding::selectRaw('audit_id, COUNT(*) as c')
            ->whereIn('audit_id', $auditIds)
            ->groupBy('audit_id')
            ->orderByDesc('c')
            ->with('audit:id,objective')
            ->limit(10)
            ->get();

        $barFindLabels = $topFindings->map(fn($r) => optional($r->audit)->objective ?? ('Audit #'.$r->audit_id));
        $barFindData   = $topFindings->pluck('c');

        // ===== Acciones correctivas por estado =====
        $actionsStatusLabels = ['Pendientes', 'En progreso', 'Completadas', 'Vencidas'];
        $actionsStatusData   = [$actionsPending, $actionsInProg, $actionsDone, $actionsOverdue];

        // ===== Carga por auditor (solo admin) =====
        $workloadLabels = [];
        $workloadData   = [];
        if ($isAdmin) {
            $workload = Audit::visible()
                ->selectRaw('assigned_user_id, COUNT(*) as c')
                ->whereNotNull('assigned_user_id')
                ->groupBy('assigned_user_id')
                ->orderByDesc('c')
                ->with('user:id,first_name,last_name,full_name,name,email')
                ->limit(12)
                ->get();

            $workloadLabels = $workload->map(function($row){
                $u = $row->user;
                $name = trim(($u->first_name ?? '').' '.($u->last_name ?? ''));
                if (!$name) $name = $u->full_name ?? $u->name ?? $u->email ?? ('User #'.$row->assigned_user_id);
                return $name;
            });

            $workloadData = $workload->pluck('c');
        }

        // ===== Últimas auditorías (tabla) =====
        $audits = (clone $auditsBase)->latest('start_date')->take(8)->get();

        return view('auditoria.dashboard.index', compact(
            'totalAudits','activeAudits','completedAudits','plannedAudits','cancelledAudits',
            'totalFindings','openFindings','reviewedFinds',
            'actionsPending','actionsInProg','actionsDone','actionsOverdue',
            'labelsMonths','dataMonths','stateCounts',
            'barFindLabels','barFindData',
            'actionsStatusLabels','actionsStatusData',
            'workloadLabels','workloadData',
            'audits'
        ));
    }

    public function create()
    {
        // Si es admin, puede asignar a un auditor
        $auditors = [];
        if (auth()->user()?->hasRole('admin')) {
            $auditors = User::query()
                ->whereJsonContains('role', 'auditor')
                ->orderByRaw("COALESCE(NULLIF(TRIM(CONCAT(first_name,' ',last_name)),''), full_name)")
                ->get(['id','first_name','last_name','full_name','email']);
        }

        return view('auditoria.dashboard.create-audit', compact('auditors'));
    }

    public function storeAudit(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'area'         => ['required','string','max:255'],
            'objective'    => ['required','string','max:255'],
            'type'         => ['required','string','in:internal,external'],
            'state'        => ['required','string','in:planned,in_progress,completed,cancelled'],
            'start_date'   => ['required','date'],
            'end_date'     => ['required','date','after_or_equal:start_date'],
            'summary_results' => ['nullable','string','max:2000'],
            'assigned_user_id' => ['nullable','exists:users,id'],
        ]);

        // Si no es admin, fuerza type/state por seguridad (opcional)
        if (!$user->hasRole('admin')) {
            $request->merge([
                'type'  => 'internal',
                'state' => 'planned',
                'assigned_user_id' => null,
            ]);
        }

        Audit::create([
            'area'            => $request->area,
            'objective'       => $request->objective,
            'type'            => $request->type,
            'state'           => $request->state,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'summary_results' => $request->summary_results,
            'user_id'           => Auth::id(), // el usuario autenticado (admin o creador)
            'assigned_user_id'  => $request->assignee ?: Auth::id(),
        ]);

        return redirect()
            ->route('auditoria.dashboard.index')
            ->with('success', 'Auditoría creada exitosamente');
    }

}
