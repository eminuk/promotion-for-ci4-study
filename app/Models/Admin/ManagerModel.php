<?php namespace App\Models\Admin;

use CodeIgniter\Model;

/**
 * manager 테이블 관리를 위한 모델
 */
class ManagerModel extends Model
{
    protected $table = 'manager';
    protected $primaryKey = 'id';

    protected $returnType = 'App\Entities\Admin\Manager';
    // protected $useSoftDeletes = false;

    protected $allowedFields = [
        'email', 'password', 'name', 'state'
    ];

    protected $useTimestamps = false;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';
    // protected $dateFormat = '';

    // protected $validationRules    = [];
    // protected $validationMessages = [];
    // protected $skipValidation     = false;

    // protected $beforeInsert = [];
    // protected $afterInsert = [];
    // protected $beforeUpdate = [];
    // protected $afterUpdate = [];
    // protected $afterFind = [];
    // protected $afterDelete = [];


    /**
     * Get Manager entity from email
     *
     * @param string $email
     * @return [\App\Entities\Admin\Manager]
     */
    public function getFromEmail(string $email): \App\Entities\Admin\Manager
    {
        return $this->where([ 'email' => $email ])->first();
    }
}

