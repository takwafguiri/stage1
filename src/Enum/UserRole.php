<?php

namespace App\Enum;

class UserRole
{
    //Roles name
    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_SEO = "ROLE_SEO";
    public const ROLE_CONTENT_MANAGER = "ROLE_CONTENT_MANAGER";
    public const ROLE_COMMERCIAL_MANAGER = "ROLE_COMMERCIAL_MANAGER";
    public const ROLE_COMMERCIAL = "ROLE_COMMERCIAL";

    public static function getAllRoles()
    {
        return [
            UserRole::ROLE_ADMIN,
            UserRole::ROLE_SEO,
            UserRole::ROLE_CONTENT_MANAGER,
            UserRole::ROLE_COMMERCIAL_MANAGER,
            UserRole::ROLE_COMMERCIAL
        ];
    }

    public static function getAllRolesWithLabel()
    {
        return [
            UserRole::ROLE_ADMIN => "Admin",
            UserRole::ROLE_SEO => "SEO",
            UserRole::ROLE_CONTENT_MANAGER => "Content manager",
            UserRole::ROLE_COMMERCIAL_MANAGER => "Commercial manager",
            UserRole::ROLE_COMMERCIAL => "Commercial"
        ];
    }
}
