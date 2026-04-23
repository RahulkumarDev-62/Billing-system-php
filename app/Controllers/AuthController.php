<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

final class AuthController extends BaseController
{
    public function adminLoginForm(): string
    {
        return $this->loginFormFor('admin', false);
    }

    public function staffLoginForm(): string
    {
        return $this->loginFormFor('staff', true);
    }

    public function branchLoginForm(): string
    {
        return $this->loginFormFor('branch', true);
    }

    public function adminLogin(): string
    {
        return $this->loginFor('admin');
    }

    public function staffLogin(): string
    {
        return $this->loginFor('staff');
    }

    public function branchLogin(): string
    {
        return $this->loginFor('branch');
    }

    public function staffRequestOtp(): string
    {
        return $this->requestOtpFor('staff');
    }

    public function branchRequestOtp(): string
    {
        return $this->requestOtpFor('branch');
    }

    public function staffVerifyOtp(): string
    {
        return $this->verifyOtpFor('staff');
    }

    public function branchVerifyOtp(): string
    {
        return $this->verifyOtpFor('branch');
    }

    private function loginFormFor(string $role, bool $allowOtp): string
    {
        return $this->view('auth/login', [
            'title' => ucfirst($role) . ' Login',
            'role' => $role,
            'allowOtp' => $allowOtp,
            'errors' => [],
        ], null);
    }

    private function loginFor(string $role): string
    {
        verify_csrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        }
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        $user = (new User())->findByEmail($email);
        if (!$user || !password_verify($password, (string) $user['password'])) {
            $errors['login'] = 'Invalid email or password.';
        }

        if ($user && (string) $user['role'] !== $role) {
            $errors['login'] = 'This account does not belong to the selected panel.';
        }

        if ($errors) {
            return $this->view('auth/login', [
                'title' => ucfirst($role) . ' Login',
                'role' => $role,
                'allowOtp' => $role !== 'admin',
                'errors' => $errors,
                'email' => $email,
            ], null);
        }

        $_SESSION['auth_user_id'] = (int) $user['id'];
        flash('success', 'Welcome back, ' . $user['name'] . '.');
        redirect(role_dashboard_path($role));
    }

    private function requestOtpFor(string $role): string
    {
        verify_csrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $errors = [];
        if ($email === '') {
            $errors['email'] = 'Email is required for OTP.';
        }

        $user = (new User())->findByEmail($email);
        if (!$user || (string) $user['role'] !== $role) {
            $errors['otp'] = 'No ' . $role . ' account found with this email.';
        }

        if ($errors) {
            return $this->view('auth/login', [
                'title' => ucfirst($role) . ' Login',
                'role' => $role,
                'allowOtp' => true,
                'errors' => $errors,
                'email' => $email,
            ], null);
        }

        $code = (string) random_int(100000, 999999);
        $_SESSION['otp'][$role][$email] = [
            'code' => $code,
            'expires_at' => time() + 300,
            'user_id' => (int) $user['id'],
        ];

        // Dev mode: surface OTP directly (replace with email transport in production).
        flash('success', 'OTP generated: ' . $code . ' (valid for 5 minutes).');

        return $this->view('auth/login', [
            'title' => ucfirst($role) . ' Login',
            'role' => $role,
            'allowOtp' => true,
            'otpStep' => true,
            'email' => $email,
            'errors' => [],
        ], null);
    }

    private function verifyOtpFor(string $role): string
    {
        verify_csrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $otp = trim((string) ($_POST['otp'] ?? ''));
        $record = $_SESSION['otp'][$role][$email] ?? null;

        $errors = [];
        if ($email === '' || $otp === '') {
            $errors['otp'] = 'Email and OTP are required.';
        }

        if (!is_array($record)) {
            $errors['otp'] = 'No OTP request found. Please request OTP first.';
        } elseif ((int) ($record['expires_at'] ?? 0) < time()) {
            $errors['otp'] = 'OTP expired. Request a new OTP.';
        } elseif (!hash_equals((string) ($record['code'] ?? ''), $otp)) {
            $errors['otp'] = 'Invalid OTP.';
        }

        if ($errors) {
            return $this->view('auth/login', [
                'title' => ucfirst($role) . ' Login',
                'role' => $role,
                'allowOtp' => true,
                'otpStep' => true,
                'email' => $email,
                'errors' => $errors,
            ], null);
        }

        $_SESSION['auth_user_id'] = (int) $record['user_id'];
        unset($_SESSION['otp'][$role][$email]);

        flash('success', 'OTP login successful.');
        redirect(role_dashboard_path($role));
    }

    public function logout(): never
    {
        unset($_SESSION['auth_user_id']);
        unset($_SESSION['otp']);
        flash('success', 'Logged out successfully.');
        redirect('/');
    }
}