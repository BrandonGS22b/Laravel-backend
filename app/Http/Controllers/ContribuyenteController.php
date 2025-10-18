<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\ContribuyenteRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use App\Helpers\ValidationHelper;
class ContribuyenteController extends Controller
{
    private $contribuyenteRepo;

    public function __construct(ContribuyenteRepositoryInterface $contribuyenteRepo)
    {
        $this->contribuyenteRepo = $contribuyenteRepo;
       
    }

    // Vista principal
    public function index()
    {
        return view('contribuyentes.index');
    }

    
    

    // Data para DataTables
    public function getData(Request $request)
    {
        $contribuyentes = $this->contribuyenteRepo->getFiltered($request)->map(function($c) {
            return [
                'id' => $c->id,
                'tipo_documento' => $c->tipo_documento,
                'documento' => $c->documento,
                'nombres' => $c->nombres,
                'apellidos' => $c->apellidos,
                'nombre_completo' => $c->nombre_completo ?? $c->nombres . ' ' . $c->apellidos, 
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



   

    // Mostrar contribuyente con todos los datos
    public function show(string $id)
    {
        try {
            $c = $this->contribuyenteRepo->findById($id);
            if (!$c) {
                return response()->json(['error' => 'Contribuyente no encontrado'], 404);
            }

            // Frecuencia de letras en nombre completo
            $nombreCompleto = $c->nombre_completo ?? ($c->nombres . ' ' . $c->apellidos);
            $texto = str_replace(' ', '', mb_strtolower($nombreCompleto, 'UTF-8'));
            $chars = preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY);
            $frecuencia = array_count_values($chars);

            return response()->json([
                'contribuyente' => $c, 
                'frecuencia' => $frecuencia
            ]);

        } catch (\Throwable $e) {
            // Manejo de errores para debug
            return response()->json([
                'error' => 'Error interno del servidor',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    // Validación y creación
    public function store(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'tipo_documento' => 'required|string|max:10',
                'documento' => 'required|string|max:20|unique:contribuyentes,documento',
                'nombres' => 'required|string|max:100',
                'apellidos' => 'nullable|string|max:100',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
                'celular' => 'nullable|string|max:20',
                'email' => 'required|email:rfc,dns|unique:contribuyentes,email',
                'usuario' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

             $data = $validator->validated();
                if (!ValidationHelper::isValidEmail($data['email'])) {
                    return response()->json(['errors' => ['email' => ['Email inválido']]], 422);
                }

            try {
                $contribuyente = $this->contribuyenteRepo->create($validator->validated());
                return response()->json([
                    'message' => '✅ Contribuyente creado correctamente',
                    'contribuyente' => $contribuyente
                ], 201);
            } catch (\Throwable $e) {
                return response()->json([
                    'error' => 'Error interno del servidor',
                    'mensaje' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()
                ], 500);
            }
        }

    // Validación y actualización
    public function update(Request $request, string $id)
    {
        $contribuyente = $this->contribuyenteRepo->findById($id);
        if (!$contribuyente) {
            return response()->json(['error' => 'Contribuyente no encontrado'], 404);
        }

        // Se utiliza Validator::make para tener mayor control sobre la respuesta AJAX
        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'required|string|max:50',
            'documento' => 'required|string|max:50|unique:contribuyentes,documento,' . $id,
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:255', 
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50', 
            'email' => 'nullable|email|max:150',
            'usuario' => 'nullable|string|max:100', 
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


            $data = $validator->validated();
                if (isset($data['email']) && !ValidationHelper::isValidEmail($data['email'])) {
                    return response()->json(['errors' => ['email' => ['Email inválido']]], 422);
                }
            // Llamamos al repositorio; aquí se procesa nombre_completo
        $this->contribuyenteRepo->update($contribuyente, $validator->validated());

        return response()->json([
            'message' => 'Contribuyente actualizado correctamente',
            'contribuyente' => $contribuyente
        ]);
    }


    public function destroy(string $id)
    {
       
        $contribuyente = $this->contribuyenteRepo->findById($id);
        if (!$contribuyente) {
            return response()->json(['error' => 'Contribuyente no encontrado'], 404);
        }

        $this->contribuyenteRepo->delete($contribuyente);

        return response()->json(['message' => 'Contribuyente eliminado correctamente']);
    }
}