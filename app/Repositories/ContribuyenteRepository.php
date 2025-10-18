<?php
namespace App\Repositories;

use App\Models\Contribuyente;
use App\Repositories\Interfaces\ContribuyenteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ContribuyenteRepository implements ContribuyenteRepositoryInterface
{
    public function getFiltered(Request $request): Collection
    {
        $query = Contribuyente::query();

        if ($search = $request->nombres) { // usamos un solo input
            $query->where(function($q) use ($search) {
                $q->where('tipo_documento', 'like', "%{$search}%")
                  ->orWhere('documento', 'like', "%{$search}%")
                  ->orWhere('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function findById(string $id): ?Contribuyente
    {
        return Contribuyente::find($id);
    }

    public function create(array $data): Contribuyente
    {
         if (!ValidationHelper::isValidEmail($data['email'])) {
                throw new \InvalidArgumentException("Email inválido: " . $data['email']);
            }
        // Generamos nombre_completo antes de crear
        $data = $this->processNombreCompleto($data);
        return Contribuyente::create($data);
    }

    public function update(Contribuyente $contribuyente, array $data): bool
    {
        // Generamos nombre_completo antes de actualizar
        $data = $this->processNombreCompleto($data);
        $contribuyente->fill($data);
        return $contribuyente->save();
    }

    public function delete(Contribuyente $contribuyente): bool
    {
        return $contribuyente->delete();
    }

    private function processNombreCompleto(array $data): array
    {
        $nombres = $data['nombres'] ?? '';
        $apellidos = $data['apellidos'] ?? '';
        $tipo = strtoupper($data['tipo_documento'] ?? '');

        if ($tipo === 'NIT') {
            $parts = explode(' ', trim($nombres . ' ' . $apellidos));
            if (count($parts) > 1) {
                if (count($parts) >= 3) {
                    $nombres = implode(' ', array_slice($parts, 0, -2));
                    $apellidos = implode(' ', array_slice($parts, -2));
                } else {
                    $nombres = $parts[0];
                    $apellidos = $parts[1] ?? '';
                }
            }
        }

        $data['nombres'] = $nombres;
        $data['apellidos'] = $apellidos;
        $data['nombre_completo'] = trim($nombres . ' ' . $apellidos);

        return $data;
    }
}
