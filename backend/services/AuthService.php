<?php

require_once BASE_PATH . '/repo/UserRepository.php';
require_once BASE_PATH . '/repo/DictListRepository.php';
require_once BASE_PATH .'/services/JWTService.php';

class AuthService {
    private UserRepository $userRepo;
    private DictListRepository $dictListRepo;

    public function __construct(){
        $this->userRepo = new UserRepository();
        $this->dictListRepo = new DictListRepository();
    }

    public function register($data){
        if (empty($data['username']) || empty($data['password'])) {
            return ["error" => "Missing required fields"];
        }

        $existingUser = $this->userRepo
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
        $uploadPath = null;

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
        try{
            $this->userRepo->beginTransaction();

            $id = $this->userRepo->createUser(
                [
                    "username" => $data['username'],
                    "password_hash" => $passwordHash,
                    "profile_picture" => $profilePicture
                ]
            );

            if (!$id) {
                throw new RuntimeException("Failed to create user");
            }

            $defaultLists = [
                ['icon'=> 'favourite.svg', 'name' => 'Избранное', 'type' => 'favorites'],
                ['icon'=> 'history.svg', 'name' => 'История', 'type' => 'history']
            ];

            foreach ($defaultLists as $list) {
                $listId = $this->dictListRepo->createSystemList(
                    $id,
                    $list['name'],
                    $list['type'],
                    $list['icon']
                );

                if (!$listId) {
                    throw new RuntimeException(
                        "Failed to create default list: {$list['name']}"
                    );
                }
            }

            $this->userRepo->commit();

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
        } catch(Exception $e) {
            $this->userRepo->rollback();

            if($uploadPath && file_exists($uploadPath)) {
                unlink($uploadPath);
            }

            return [
                "error" => "Registration failed. Please try again."
            ];

        }
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

        $user = $this->userRepo
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

        $user = $this->userRepo->getUserById($userId);

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