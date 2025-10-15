<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('usuarios.index');
    }

    public function getData()
    {
        $users = User::with('role')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_name' => $user->role->name ?? 'Sin Rol',
            ];
        });

        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Usuario creado correctamente', 'user' => $user]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        if (request()->ajax()) {
            return response()->json($usuario);
        }

        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
        'role_id' => 'required|exists:roles,id',
        'password' => 'nullable|string|min:6|confirmed', // <-- Ya no es obligatorio
    ]);

    $usuario->fill([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'role_id' => $validated['role_id'],
    ]);

    // Solo actualizar contraseña si se ingresó
    if (!empty($validated['password'])) {
        $usuario->password = Hash::make($validated['password']);
    }

    $usuario->save();

    if ($request->ajax()) {
        return response()->json(['message' => 'Usuario actualizado correctamente', 'user' => $usuario]);
    }

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }
// Método para eliminar un usuario.
     
    public function destroy(Request $request, User $usuario)
    {
        try {
            $usuario->delete();

            // Si la petición viene por AJAX (fetch o jQuery)
            if ($request->ajax()) {
                return response()->json(['message' => 'Usuario eliminado correctamente.']);
            }

            // Si es una petición normal
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'No se pudo eliminar el usuario. ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('usuarios.index')->with('error', 'No se pudo eliminar el usuario.');
        }
    }
}