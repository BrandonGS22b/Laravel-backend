<?php

namespace App\Repositories\Interfaces;

use App\Models\Contribuyente;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface ContribuyenteRepositoryInterface
{
    /**
     * Obtiene y filtra contribuyentes. La lógica de filtrado se mantiene aquí por ser una consulta de BD pura.
     */
    public function getFiltered(Request $request): Collection;
    
    /**
     * Busca un contribuyente por ID.
     */
    public function findById(string $id): ?Contribuyente;

    /**
     * Crea un nuevo contribuyente.
     */
    public function create(array $data): Contribuyente;

    /**
     * Actualiza un contribuyente.
     */
    public function update(Contribuyente $contribuyente, array $data): bool;

    /**
     * Elimina un contribuyente.
     */
    public function delete(Contribuyente $contribuyente): bool;
}
