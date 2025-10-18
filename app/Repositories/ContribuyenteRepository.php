<?php
/*Aquí estás viendo la implementación concreta del patrón repositorio.
Este archivo (ContribuyenteRepository) es el que realmente hace el trabajo de acceder a la base de datos usando el modelo Eloquent. */
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
        // El Contribuyente Model ya maneja la lógica de NIT/nombre completo en el booted()
        return Contribuyente::create($data);
    }

    public function update(Contribuyente $contribuyente, array $data): bool
    {
        // Usamos fill/save para que los eventos del modelo (booted) sigan funcionando
        $contribuyente->fill($data);
        return $contribuyente->save();
    }

    public function delete(Contribuyente $contribuyente): bool
    {
        return $contribuyente->delete();
    }
}
