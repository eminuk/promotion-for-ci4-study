<?php namespace App\Entities\Admin;

use CodeIgniter\Entity;
use App\Libraries;

/**
 * Manager 관리를 위한 엔티티
 */
class Manager extends Entity
{

    private $commonLib;

    public function __construct()
    {
        parent::__construct();

        $this->commonLib = new Libraries\CommonLib();
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