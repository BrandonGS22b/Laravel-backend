<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(array $data): User
    {
        // 🚨 Lógica de negocio movida aquí: Hashear la contraseña
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    public function update(User $user, array $data): bool
    {
        // 🚨 Lógica de negocio movida aquí: Solo hashear si la contraseña existe
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // No actualizar si está vacío
        }

        return $this->userRepository->update($user, $data);
    }
}
