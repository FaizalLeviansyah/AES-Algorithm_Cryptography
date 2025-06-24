<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('is-admin-or-master-divisi');
        $user = auth()->user();
        if (Gate::allows('is-admin')) {
            $users = User::with('division')->orderBy('fullname')->get();
        } else {
            $users = User::where('division_id', $user->division_id)->with('division')->orderBy('fullname')->get();
        }
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('is-admin-or-master-divisi');
        $divisions = Division::orderBy('division_name')->get();
        return view('users.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $this->authorize('is-admin-or-master-divisi');
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'level' => ['required', 'in:Admin,Master Divisi,Master User'],
            'division_id' => ['required', 'exists:divisions,id'],
        ]);
        if (auth()->user()->level === 'Master Divisi' && $request->level === 'Admin') {
            abort(403, 'Anda tidak memiliki hak untuk membuat user Admin.');
        }
        User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'division_id' => $request->division_id,
        ]);
        return redirect()->route('users.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->authorize('manage-user', $user);
        $divisions = Division::orderBy('division_name')->get();
        return view('users.edit', compact('user', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('manage-user', $user);
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'level' => ['required', 'in:Admin,Master Divisi,Master User'],
            'division_id' => ['required', 'exists:divisions,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);
        if (auth()->user()->level === 'Master Divisi' && $request->level === 'Admin') {
            abort(403, 'Anda tidak memiliki hak untuk mengubah user menjadi Admin.');
        }
        $user->update([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'level' => $request->level,
            'division_id' => $request->division_id,
        ]);
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('manage-user', $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
