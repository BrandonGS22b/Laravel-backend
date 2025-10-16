<?php

namespace App\Http\Controllers;

use App\Models\Contribuyente;
use App\Repositories\Interfaces\ContribuyenteRepositoryInterface;
use Illuminate\Http\Request;

class ContribuyenteController extends Controller
{
    private $contribuyenteRepo;

    public function __construct(ContribuyenteRepositoryInterface $contribuyenteRepo)
    {
        $this->contribuyenteRepo = $contribuyenteRepo;
    }

    public function index()
    {
        return view('contribuyentes.index');
    }

    public function getData(Request $request)
    {
        $contribuyentes = $this->contribuyenteRepo->getFiltered($request)->map(function($c) {
            return [
                'id' => $c->id,
                'tipo_documento' => $c->tipo_documento,
                'documento' => $c->documento,
                'nombres' => $c->nombres,
                'apellidos' => $c->apellidos,
                'nombre_completo' => $c->nombre_completo,
                'telefono' => $c->telefono,
                'celular' => $c->celular,
                'email' => $c->email,
                'usuario' => $c->usuario,
                'created_at' => $c->created_at,
                'updated_at' => $c->updated_at,
            ];
        });

        return response()->json(['data' => $contribuyentes]);
    }

   public function show(string $id)
    {
        try {
            $c = $this->contribuyenteRepo->findById($id);
            if (!$c) {
                return response()->json(['error' => 'Contribuyente no encontrado'], 404);
            }

            // Texto con soporte UTF-8
            $texto = $c->nombre_completo;
            $texto = str_replace(' ', '', mb_strtolower($texto, 'UTF-8'));

            // Separar correctamente cada carácter Unicode
            $chars = preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY);

            // Calcular la frecuencia
            $frecuencia = array_count_values($chars);

            return response()->json([
                'contribuyente' => $c,
                'frecuencia' => $frecuencia
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error interno del servidor',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }




    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|string|max:50',
            'documento' => 'required|string|max:50|unique:contribuyentes,documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'usuario' => 'nullable|string|max:100',
        ]);

        $contribuyente = $this->contribuyenteRepo->create($validated);

        return response()->json(['message' => 'Contribuyente creado correctamente', 'contribuyente' => $contribuyente]);
    }

    public function update(Request $request, string $id)
    {
        $contribuyente = $this->contribuyenteRepo->findById($id);
        if (!$contribuyente) return response()->json(['error' => 'Contribuyente no encontrado'], 404);

        $validated = $request->validate([
            'tipo_documento' => 'required|string|max:50',
            'documento' => 'required|string|max:50|unique:contribuyentes,documento,' . $id,
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'usuario' => 'nullable|string|max:100',
        ]);

        $this->contribuyenteRepo->update($contribuyente, $validated);

        return response()->json(['message' => 'Contribuyente actualizado correctamente', 'contribuyente' => $contribuyente]);
    }

    public function destroy(string $id)
    {
        $contribuyente = $this->contribuyenteRepo->findById($id);
        if (!$contribuyente) return response()->json(['error' => 'Contribuyente no encontrado'], 404);

        $this->contribuyenteRepo->delete($contribuyente);

        return response()->json(['message' => 'Contribuyente eliminado correctamente']);
    }
}
