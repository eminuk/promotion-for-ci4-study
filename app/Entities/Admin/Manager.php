<?php namespace App\Entities\Admin;

use CodeIgniter\Entity;

/**
 * Manager 관리를 위한 엔티티
 */
class Manager extends Entity
{
    public function setPassword(string $password) {
        $this->attributes['password'] = hash('SHA256', "salt{$password}autocarz");
        return $this;
    }
}