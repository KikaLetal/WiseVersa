<?php

namespace App\Services;

use App\Models\User;
use App\Models\DictLists;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class UserService
{
    public function findByUsername(string $username) {
        return User::where('username', $username)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function loginUser($data){
        $user = $this->findByUsername($data['username']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    public function createUser(array $data, $avatarFile = null): ?User
    {
        $profilePicture = 'default.png';
        $uploadedPath = null;

        if ($avatarFile) {
            $uploadedPath = $avatarFile->store('avatars', 'public');
            $profilePicture = basename($uploadedPath); 
        }

        try {
            return DB::transaction(function () use ($data, $profilePicture) {
                $user = User::create([
                    'username'        => $data['username'],
                    'password'        => $data['password'],
                    'profile_picture' => $profilePicture,
                    'role'            => $data['role'] ?? 'user',
                ]);

                DictLists::create([
                    'user_id' => $user->id,
                    'name'    => 'Избранное',
                    'type'    => 'favorites',
                    'icon'    => 'star'
                ]);

                DictLists::create([
                    'user_id' => $user->id,
                    'name'    => 'История',
                    'type'    => 'history',
                    'icon'    => 'history'
                ]);

                return $user;
            });
        } catch(Exception $e){
            if ($uploadedPath) {
                Storage::disk('public')->delete($uploadedPath);
            }

            throw new \RuntimeException("Ошибка при создании пользователя: " . $e->getMessage());
        }
    }
}