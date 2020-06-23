<?php namespace App\Libraries;

/**
 * Common method library used by the application
 */
class CommonLib
{
    /**
     * Get hashed string to use password
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return hash('SHA256', "salt{$password}tlsa");
    }

}

