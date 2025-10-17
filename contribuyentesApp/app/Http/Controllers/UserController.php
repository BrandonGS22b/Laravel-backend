<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        return view('usuarios.index');
    }

    public function getData()
    {
        $users = $this->userRepo->all()->map(function ($user) {
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

        // Preparo los datos para enviarlos al repositorio
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ];

        $user = $this->userRepo->create($data);

        if ($request->ajax()) {
            return response()->json(['message' => 'Usuario creado correctamente', 'user' => $user]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if (request()->ajax()) {
            return response()->json($user);
        }

        $roles = Role::all();
        return view('usuarios.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $this->userRepo->update($user, $data);

        if ($request->ajax()) {
            return response()->json(['message' => 'Usuario actualizado correctamente', 'user' => $user]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        try {
            $this->userRepo->delete($user);

            if ($request->ajax()) {
                return response()->json(['message' => 'Usuario eliminado correctamente.']);
            }

            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error al eliminar usuario: ' . $e->getMessage()], 500);
            }

            return redirect()->route('usuarios.index')->with('error', 'No se pudo eliminar el usuario.');
        }
    }
}
