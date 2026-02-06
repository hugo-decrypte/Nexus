<?php

namespace application_core\application\usecases;

use application_core\exceptions\InsufficientRightsAuthzException;
use application_core\exceptions\InvalidRoleAuthzException;
use application_core\exceptions\NotOwnerAuthzException;

class AuthzUserService
{
    const ROLE_CLIENT     = 10;
    const ROLE_COMMERCANT  = 50;
    const ROLE_ADMIN      = 100;

//    public function isGranted(string $user_id, string $role, ?string $ressource_id, int $requiredRole = self::ROLE_CLIENT): bool
    public function isGranted(string $role, int $requiredRole = self::ROLE_CLIENT): bool
    {
        $r = 0;
        switch ($role) {
            case 'client':
                $r = self::ROLE_CLIENT;
                break;
            case 'commercant':
                $r = self::ROLE_COMMERCANT;
                break;
            case 'admin':
                $r = self::ROLE_ADMIN;
                break;
            default:
                $r = 1;
        }

        if ($r < self::ROLE_CLIENT) {
            throw new InvalidRoleAuthzException('Accès refusé : rôle inconnu.');
        }

        if ($r === self::ROLE_ADMIN) {
            return true;
        }

        if ($r < $requiredRole) {
            throw new InsufficientRightsAuthzException("Niveau d'accès insuffisant");
        }

//        if ($ressource_id !== null && $user_id !== $ressource_id) {
//            throw new NotOwnerAuthzException('Accès refusé : vous n\'êtes pas propriétaire de cette ressource.');
//        }

        return true;
    }
}