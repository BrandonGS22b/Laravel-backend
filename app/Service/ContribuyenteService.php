<?php

namespace App\Services;

use App\Repositories\Interfaces\ContribuyenteRepositoryInterface;
use App\Models\Contribuyente;

class ContribuyenteService
{
    protected $contribuyenteRepository;

    public function __construct(ContribuyenteRepositoryInterface $contribuyenteRepository)
    {
        $this->contribuyenteRepository = $contribuyenteRepository;
    }

    public function prepareAndCreate(array $data): Contribuyente
    {
        // 🚨 Lógica de NEGOCIO: Separar nombres si es NIT
        $preparedData = $this->handleNitLogic($data); 
        
        // 🚨 Lógica de NEGOCIO: Fijar la fecha de registro
        $preparedData['fecha_registro'] = now(); 

        // Llama al Repositorio para la acción final (Guardar en BD)
        return $this->contribuyenteRepository->create($preparedData); 
    }
    
    // Método privado que contiene la lógica específica del NIT
    protected function handleNitLogic(array $data): array
    {
        // ... Lógica que era muy larga en el Controller, ahora encapsulada aquí ...
        return $data;
    }
    
    // ... (Otros métodos de Servicio, como update, delete) ...
}
