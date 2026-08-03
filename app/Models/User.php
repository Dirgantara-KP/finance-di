<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/*
 * Keycloak Integration Notes
 * ==========================
 *
 * This app is planned to use Keycloak as the primary identity provider (SSO).
 * When Keycloak is fully integrated (e.g. via a middleware guard or
 * socialite provider), authentication will be handled by Keycloak — not by
 * Laravel's built-in email/password session auth.
 *
 * Impact on the users table:
 * - password column can be removed or made nullable (Keycloak handles
 *   password verification)
 * - remember_token may become irrelevant (sessions managed by Keycloak JWT)
 * - Additional columns may be needed:
 *     keycloak_id   – unique user ID from Keycloak's `sub` claim (string)
 *     username      – Keycloak username (string, nullable)
 *     nik           – employee ID number (string, nullable)
 *     jabatan       – position/title (string, nullable)
 *     phone         – phone number (string, nullable)
 *     c_org_cur     – cost centre / unit code (char(6), nullable)
 *     is_active     – user active status (boolean, default true)
 *
 * Auth guard changes (config/auth.php):
 *     When Keycloak is live, replace the 'web' guard's driver and provider
 *     with a Keycloak adapter (e.g. robertfausk/laravel-keycloak-guard).
 *
 * Filament panel changes (AdminPanelProvider.php):
 *     If Keycloak handles auth, `->login()` may need to be removed or
 *     replaced with a keycloak-based route.
 */

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
