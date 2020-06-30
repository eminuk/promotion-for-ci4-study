<?php namespace App\Entities\Admin;

use CodeIgniter\Entity;

/**
 * Manager 관리를 위한 엔티티
 */
class Manager extends Entity
{
    /**
     * commonLib Library
     *
     * @var [type]
     */
    private $commonLib;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        // Load libraries
        $this->commonLib = new \App\Libraries\CommonLib();
    }

    /**
     * Check manager's password and status
     *
     * @param string $password
     * @return boolean
     */
    public function checkPassword(string $password): bool
    {
        return (
            $this->attributes['status'] == '1' 
            && $this->attributes['password'] === $this->commonLib->hashPassword($password)
        );
    }
}