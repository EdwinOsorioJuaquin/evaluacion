<?php

namespace App\Http\Controllers\Auditoria;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Asegura solo admins:
        $this->middleware(function ($request, $next) {
            if (!auth()->user()?->hasRole('admin')) abort(403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $status = $request->get('status', 'active'); // active|inactive|all

        $users = User::query()
            ->where(fn($w) =>
                $w->whereJsonContains('role', 'admin')
                  ->orWhereJsonContains('role', 'auditor')
            )
            ->when($q, function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('first_name', 'like', "%$q%")
                      ->orWhere('last_name', 'like', "%$q%")
                      ->orWhere('email', 'like', "%$q%");
                });
            })
            ->when($status !== 'all', function ($w) use ($status) {
                $w->where('status', $status === 'inactive' ? 'inactive' : 'active');
            })
            ->orderBy('last_name')->orderBy('first_name')
            ->paginate(12)
            ->withQueryString();

        return view('auditoria.auditores.index', compact('users', 'q', 'status'));
    }

    public function create()
    {
        $roles = ['admin' => 'Administrador', 'auditor' => 'Auditor'];
        return view('auditoria.auditores.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'   => ['required','string','max:80'],
            'last_name'    => ['required','string','max:80'],
            'email'        => ['required','email','max:255','unique:users,email'],
            'phone_number' => ['nullable','string','max:30'],
            'role'         => ['required', Rule::in(['admin','auditor'])],
            'status'       => ['required', Rule::in(['active','inactive'])],
            'password'     => ['required','confirmed','min:8'],
        ]);

        $user = User::create([
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'full_name'    => trim($data['first_name'].' '.$data['last_name']),
            'email'        => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'role'         => [$data['role']],              // JSON por cast
            'status'       => $data['status'],
            'password'     => Hash::make($data['password']),
        ]);

        return redirect()->route('auditoria.auditores.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        if (! $user->hasRole(['admin','auditor'])) abort(404);
        $roles = ['admin' => 'Administrador', 'auditor' => 'Auditor'];
        return view('auditoria.auditores.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        if (! $user->hasRole(['admin','auditor'])) abort(404);

        $data = $request->validate([
            'first_name'   => ['required','string','max:80'],
            'last_name'    => ['required','string','max:80'],
            'email'        => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone_number' => ['nullable','string','max:30'],
            'role'         => ['required', Rule::in(['admin','auditor'])],
            'status'       => ['required', Rule::in(['active','inactive'])],
            'password'     => ['nullable','confirmed','min:8'],
        ]);

        $user->first_name   = $data['first_name'];
        $user->last_name    = $data['last_name'];
        $user->full_name    = trim($data['first_name'].' '.$data['last_name']);
        $user->email        = $data['email'];
        $user->phone_number = $data['phone_number'] ?? null;
        $user->role         = [$data['role']];
        $user->status       = $data['status'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()->route('auditoria.auditores.index')->with('success', 'Usuario actualizado.');
    }

    // Activar/Inactivar (en lugar de borrar duro)
    public function toggleStatus(User $user)
    {
        if (! $user->hasRole(['admin','auditor'])) abort(404);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'Estado actualizado.');
    }
}
