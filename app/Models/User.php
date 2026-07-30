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
 * Impact on the USERS table:
 * - PASSWORD column can be removed or made nullable (Keycloak handles
 *   password verification)
 * - REMEMBER_TOKEN may become irrelevant (sessions managed by Keycloak JWT)
 * - Additional columns may be needed:
 *     KEYCLOAK_ID   – unique user ID from Keycloak's `sub` claim (string)
 *     USERNAME      – Keycloak username (string, nullable)
 *     NIK           – employee ID number (string, nullable)
 *     JABATAN       – position/title (string, nullable)
 *     PHONE         – phone number (string, nullable)
 *     C_ORG_CUR     – cost centre / unit code (char(6), nullable)
 *     IS_ACTIVE     – user active status (boolean, default true)
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

    protected $table = 'USERS';

    protected $fillable = [
        'NAME',
        'EMAIL',
        'PASSWORD',
        'EMAIL_VERIFIED_AT',
        'remember_token',
    ];

    protected $hidden = [
        'PASSWORD',
        'remember_token',
    ];

    public function getAuthPassword(): string
    {
        return $this->PASSWORD;
    }

    /*
     * Override fill() to normalise attribute keys to uppercase.
     * This ensures external code (e.g. Filament's make:filament-user command)
     * that passes lowercase keys like 'name', 'email', 'password' still
     * matches the USERS table's uppercase column names.
     */
    public function fill(array $attributes)
    {
        $normalised = [];

        foreach ($attributes as $key => $value) {
            $normalised[strtoupper($key)] = $value;
        }

        return parent::fill($normalised);
    }

    protected function casts(): array
    {
        return [
            'EMAIL_VERIFIED_AT' => 'datetime',
            'PASSWORD' => 'hashed',
        ];
    }
}
