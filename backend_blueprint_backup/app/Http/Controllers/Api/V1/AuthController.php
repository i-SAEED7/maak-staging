<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

final class AuthController
{
    public function login(): void
    {
        // Validate credentials via LoginRequest.
        // Delegate to AuthService::login.
        // Return token and current user payload.
    }

    public function logout(): void
    {
        // Revoke current session token.
    }

    public function me(): void
    {
        // Return current authenticated user and permissions.
    }

    public function forgotPassword(): void
    {
        // Trigger OTP generation and delivery.
    }

    public function verifyResetOtp(): void
    {
        // Validate OTP before password reset.
    }

    public function resetPassword(): void
    {
        // Persist new password and consume OTP.
    }

    public function changePassword(): void
    {
        // Change password for authenticated user.
    }
}
