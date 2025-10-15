<?php

namespace App\Http\Controllers;

use App\Helpers\ContarLetrasHelper;
use App\Models\Contribuyente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContribuyenteController extends Controller
{
    /**
     * Mostrar todos los contribuyentes.
     */
   public function index(Request $request)
{
    $query = Contribuyente::query();

    foreach (['tipo_documento','documento','nombres','apellidos','telefono'] as $campo) {
        if ($request->filled($campo)) {
            $query->where($campo, 'like', "%{$request->$campo}%");
        }
    }

    $contribuyentes = $query->orderBy('id', 'desc')->get();

 
    return response()->json([
        'data' => $contribuyentes
    ]);
    
}

    /**
     * Guardar un nuevo contribuyente.
     *Incluye validación y generación de nombre completo.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'required|string|max:10',
            'documento'      => 'required|string|max:20|unique:contribuyentes,documento',
            'nombres'        => 'required|string|max:100',
            'apellidos'      => 'required|string|max:100',
            'direccion'      => 'nullable|string|max:255',
            'telefono'       => 'nullable|string|max:20',
            'celular'        => 'nullable|string|max:20',
            'email'          => 'required|email|unique:contribuyentes,email',
            'usuario'        => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //  Lógica especial para tipo de documento NIT
        $nombres = $request->nombres;
        $apellidos = $request->apellidos;

        if (strtoupper($request->tipo_documento) === 'NIT') {
            // separa por espacios el valor ingresado en “nombres”
            $partes = explode(' ', trim($request->nombres));
            if (count($partes) > 1) {
                $nombres = implode(' ', array_slice($partes, 0, -2));
                $apellidos = implode(' ', array_slice($partes, -2));
            }
        }

        //  Generar nombre completo automáticamente
        $nombreCompleto = trim($nombres . ' ' . $apellidos);

        //  Guardar en base de datos
        $contribuyente = Contribuyente::create([
            'tipo_documento'  => strtoupper($request->tipo_documento),
            'documento'       => $request->documento,
            'nombres'         => $nombres,
            'apellidos'       => $apellidos,
            'nombre_completo' => $nombreCompleto,
            'direccion'       => $request->direccion,
            'telefono'        => $request->telefono,
            'celular'         => $request->celular,
            'email'           => $request->email,
            'usuario'         => $request->usuario,
            'fecha_registro'  => now(),
        ]);

        return response()->json([
            'message' => 'Contribuyente creado correctamente',
            'data'    => $contribuyente
        ], 201);
    }

    /**
     * Mostrar un contribuyente específico.
     */
    public function show(string $id)
    {
        $contribuyente = Contribuyente::find($id);

        if (!$contribuyente) {
            return response()->json(['message' => 'Contribuyente no encontrado'], 404);
        }

        //Calcular frecuencia de letras con helper recursivo
        $texto = $contribuyente->nombres . ' ' . $contribuyente->apellidos;
        $frecuencia = ContarLetrasHelper::contar($texto);

        return response()->json([
            'contribuyente' => $contribuyente,
            'frecuencia' => $frecuencia
        ]);
    }

    /**
     * Actualizar un contribuyente existente.
     */
    public function update(Request $request, string $id)
    {
        $contribuyente = Contribuyente::find($id);

        if (!$contribuyente) {
            return response()->json(['message' => 'Contribuyente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'sometimes|required|string|max:10',
            'documento'      => 'sometimes|required|string|max:20|unique:contribuyentes,documento,' . $id,
            'nombres'        => 'sometimes|required|string|max:100',
            'apellidos'      => 'sometimes|required|string|max:100',
            'direccion'      => 'nullable|string|max:255',
            'telefono'       => 'nullable|string|max:20',
            'celular'        => 'nullable|string|max:20',
            'email'          => 'sometimes|required|email|unique:contribuyentes,email,' . $id,
            'usuario'        => 'sometimes|required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contribuyente->fill($request->only([
            'tipo_documento', 'documento', 'nombres', 'apellidos',
            'direccion', 'telefono', 'celular', 'email', 'usuario'
        ]));

        // 🔹 Recalcular nombre completo si cambió nombres o apellidos
        if ($request->has('nombres') || $request->has('apellidos')) {
            $contribuyente->nombre_completo = trim(($request->nombres ?? $contribuyente->nombres) . ' ' . ($request->apellidos ?? $contribuyente->apellidos));
        }

        $contribuyente->save();

        return response()->json([
            'message' => '✅ Contribuyente actualizado correctamente',
            'data' => $contribuyente
        ]);
    }

    /**
     * Eliminar un contribuyente.
     */
    public function destroy(string $id)
    {
        $contribuyente = Contribuyente::find($id);

        if (!$contribuyente) {
            return response()->json(['message' => 'Contribuyente no encontrado'], 404);
        }

        $contribuyente->delete();

        return response()->json(['message' => '🗑️ Contribuyente eliminado correctamente']);
    }
}
