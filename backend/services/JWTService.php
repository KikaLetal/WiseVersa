<?php

class JWTService{
    private string $secretKey = "Super_SECRET_KEY_123";

    private function base64UrlEncode($data){
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    }

    private function base64UrlDecode($data){
        return base64_decode(
            strtr($data, '-_', '+/')
        );
    }

    public function generateToken($user){

        $header = [
            "alg" => "HS256",
            "typ" => "JWT"
        ];

        $payload = [
            "id" => $user['id'],
            "username" => $user['username'],
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24 * 30)
        ];

        $headerEncoded = $this->base64UrlEncode(
            json_encode($header)
        );

        $payloadEncoded = $this->base64UrlEncode(
            json_encode($payload)
        );

        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            $this->secretKey,
            true
        );

        $signatureEncoded = $this->base64UrlEncode(
            $signature
        );

        return "$headerEncoded.$payloadEncoded.$signatureEncoded";

    }

    public function validateToken($token){
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [
            $headerEncoded, 
            $payloadEncoded, 
            $signatureEncoded
        ] = $parts;

        $expectedSignature = $this->base64UrlEncode(
            hash_hmac(
                'sha256',
                "$headerEncoded.$payloadEncoded",
                $this->secretKey,
                true
            )
        );

        if (!hash_equals(
            $expectedSignature, 
            $signatureEncoded)
        ) {
            return null;
        }

        $payloadJson = json_decode(
            $this->base64UrlDecode($payloadEncoded),
            true
        );

        if (!$payloadJson || !isset($payloadJson['exp'])) {
            return null;
        }

        if ($payloadJson['exp'] < time()) {
            return null;
        }

        return $payloadJson;
    } 
}