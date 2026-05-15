<?php

require_once BASE_PATH . '/repo/UserRepository.php';
require_once BASE_PATH .'/services/JWTService.php';
require_once BASE_PATH . '/repo/DictListRepository.php';

class AuthService {
    private UserRepository $repo;
    private DictListRepository $dictRepo;

    public function __construct(){
        $this->repo = new UserRepository();
        $this->dictRepo = new DictListRepository();
    }

    private function createDefaultLists($userId) {
        $this->dictRepo->createSystemList($userId, 'Избранное', 'favorites');

        $this->dictRepo->createSystemList($userId, 'История', 'history');
    }

    public function register($data){
        if (empty($data['username']) || empty($data['password'])) {
            return ["error" => "Missing required fields"];
        }

        $existingUser = $this->repo
        ->getUserByUsername($data['username']);

        if ($existingUser) {
            return[
                "error" => "Username already exists"
            ];
        }

        $passwordHash = password_hash(
            $data['password'], 
            PASSWORD_DEFAULT
        );

        $profilePicture = 'default.png';

        if(!empty($data['avatar']) && $data['avatar']['tmp_name']) {
            $extension = pathinfo(
                $data['avatar']['name'], 
                PATHINFO_EXTENSION
            );

            $fileName = uniqid() . "." . $extension;

            $uploadPath = BASE_PATH . "/uploads/" . $fileName;

            if (!move_uploaded_file($data['avatar']['tmp_name'], $uploadPath)) {
                return [
                    "error" => "Failed to upload avatar"
                ];
            }

            $profilePicture = $fileName;
        }

        $id = $this->repo->createUser([
            "username" => $data['username'],
            "password_hash" => $passwordHash,
            "profile_picture" => $profilePicture
        ]);

        $this->createDefaultLists($id);

        $jwtService = new JWTService();

        $token = $jwtService->generateToken([
            'id' => $id,
            'username' => $data['username']
        ]);

        return [
            "token" => $token,
            'user' => [
                'id' => $id,
                'username' => $data['username']
            ]
        ];
    }

    public function login($data){

        if (
            !isset($data['username']) ||
            !isset($data['password'])
        ) {
            return[
                "error" => "Missing required fields"
            ];
        }

        $user = $this->repo
        ->getUserByUsername($data['username']);

        if (!$user) {
            return[
                "error" => "Wrong username or password"
            ];
        }

        $isPasswordValid = password_verify(
            $data['password'], 
            $user['password_hash']
        );

        if (!$isPasswordValid) {
            return[
                "error" => "Wrong username or password"
            ];
        }

        $jwtService = new JWTService();
        $token = $jwtService->generateToken([
            'id' => $user['id'],
            'username' => $user['username']
        ]);

        return [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'profile_picture' => $user['profile_picture']
            ]
        ];
    }

    public function meById($userId){

        $user = $this->repo->getUserById($userId);

        if (!$user) {
            return ["error" => "User not found"];
        }

        return [
            "user" => [
                'id' => $user['id'],
                'username' => $user['username'],
                'profile_picture' => $user['profile_picture']
            ]
        ];
    }
}