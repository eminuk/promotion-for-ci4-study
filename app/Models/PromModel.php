<?php namespace App\Models;

use CodeIgniter\Model;


class PromModel extends Model
{
    // protected $DBGroup = '';
    // protected $table = '';
    // protected $primaryKey = '/';

    protected $returnType = 'App\Entities\Prom';
    // protected $useSoftDeletes = false;

    // protected $allowedFields = [];

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
     * Construct
     */
    public function __construct()
    {
        // parent's construct
        parent::__construct();
    }

    /**
     * Get promotion list
     *
     * @param array $params
     * @param boolean $use_paging
     * @return array
     */
    public function getPromList(array $params = [], bool $use_paging = true): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'list' => [], 'total_rows' => 0);


        // Get list
        $sql_where = '';
        $sql_params = [];

        // 검색조건 - 신청여부
        switch ($params['is_select']) {
            case 'ALL':
                break;
            case 'Y':
                $sql_where .= "AND kc.product_id IS NOT NULL ";
                break;
            case 'N':
                $sql_where .= "AND kc.product_id IS NULL ";
                break;
            default:
                $rtn['message'] = '잘못된 검색 설정입니다.';
                return $rtn;
                break;
        }

        // 검색조건 - 조회기간
        if (!empty($params['sdate'])) {
            $sql_where .= "AND kc.select_at >= :sdate: ";
            $sql_params['sdate'] = date('Y-m-d 00:00:00', strtotime($params['sdate']));
        }
        if (!empty($params['edate'])) {
            $sql_where .= "AND kc.select_at <= :edate: ";
            $sql_params['edate'] = date('Y-m-d 23:59:59', strtotime($params['edate']));
        }

        // 검색조건 - 검색어
        if (!empty($params['search_value'])) {
            switch ($params['search_key']) {
                case 'pm_code':
                    $sql_where .= "AND kp.items LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'cus_name':
                    $sql_where .= "AND k.cus_name LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'cus_mobile':
                    $sql_where .= "AND k.cus_mobile LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'cus_zip':
                    $sql_where .= "AND kc.cus_zip LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'cus_addr1':
                    $sql_where .= "AND kc.cus_addr1 LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'cus_addr2':
                    $sql_where .= "AND kc.cus_addr2 LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'bnft_price':
                    $sql_where .= "AND k.bnft_price LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'bnft_code':
                    $sql_where .= "AND kc.bnft_code LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                default:
                    $rtn['message'] = '잘못된 검색 설정입니다.';
                    return $rtn;
                    break;
            }
        }

        // 페이징
        $offset = ($params['page_num'] - 1) * $params['page_size'];
        $sql_limit = '';
        if ($use_paging) {
            $sql_limit = "LIMIT ".$this->db->escapeString($offset).", ".$this->db->escapeString($params['page_size'])." ";
        }

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                k.id, k.pm_number, k.pm_code, 
                k.cus_name, k.cus_mobile, 
                k.bnft_price, 
                kc.bnft_code, IF(kc.product_id IS NULL, '미신청', '신청완료') AS is_select_kr, kc.select_at, 
                kc.cus_zip AS customer_zip, kc.cus_addr1 AS customer_addr1, kc.cus_addr2 AS customer_addr2, kc.send_sms, 
                kp.id AS product_id, kp.type, kp.items, 
                CASE kp.type 
                    WHEN 1 THEN '옵션 1' 
                    WHEN 2 THEN '옵션 2' 
                    WHEN 3 THEN '옵션 3' 
                    ELSE '' 
                END AS type_kr 
            FROM promotion AS k 
                JOIN promotion_customer AS kc ON k.id = kc.pm_id 
                LEFT JOIN promotion_product AS kp ON kc.product_id = kp.id 
            WHERE k.status = 1 
                {$sql_where}
            ORDER BY k.id DESC 
            {$sql_limit}
            ;
        ";
        $query = $this->query($sql, $sql_params);

        $rtn['list'] = $query->getResultArray();
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }
        $query->freeResult();


        // Get total count
        $query = $this->query("SELECT FOUND_ROWS() AS total_rows; ");
        $rtn['total_rows'] = $query->getRowArray()['total_rows'];
        $query->freeResult();


        $rtn['result'] = true;

        return $rtn;
    }

    /**
     * Get Kw promotion info
     *
     * @param integer $pm_id
     * @return array
     */
    public function getKwInfo(int $pm_id = 0): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'row' => []);


        // Get list
        $sql_params = [
            'pm_id' => $pm_id
        ];

        $sql = "
            SELECT 
                k.id, k.pm_code, 
                k.cus_name, k.cus_mobile, k.bnft_price, 
                kc.bnft_code, IF(kc.product_id IS NULL, '미신청', '신청완료') AS is_select_kr, kc.select_at, 
                kc.cus_zip AS customer_zip, kc.cus_addr1 AS customer_addr1, kc.cus_addr2 AS customer_addr2, kc.send_sms, 
                CASE kc.send_sms 
                    WHEN 0 THEN '미발송' 
                    WHEN 1 THEN '발송완료' 
                    WHEN 2 THEN '발송실패' 
                    WHEN 3 THEN '재발송요청' 
                    ELSE '-' 
                END AS send_sms_kr, 
                kp.type, kp.items  
            FROM promotion AS k 
                JOIN promotion_customer AS kc ON k.id = kc.pm_id 
                LEFT JOIN promotion_product AS kp ON kc.product_id = kp.id 
            WHERE k.status = 1 AND k.id = :pm_id:
            LIMIT 1 
            ;
        ";
        $query = $this->query($sql, $sql_params);

        $rtn['row'] = $query->getRowArray();
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }
        $query->freeResult();


        $rtn['result'] = true;

        return $rtn;
    }

    /**
     * Delete promotion data
     *
     * @param array $pm_ids
     * @return array
     */
    public function deleteData(array $pm_ids = []): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);


        // Set sql parameters
        $sql_params = [
            'pm_ids' => $pm_ids
        ];


        // Transactions - start
        $this->db->transBegin();


        // Delete promotion data
        $sql = "
            DELETE FROM promotion 
            WHERE id IN :pm_ids: 
            ;
        ";
        $query = $this->db->query($sql, $sql_params);
        $error = $this->error();
        if ($error['code'] !== 0) {
            // Transactions - rollback
            $this->db->transRollback();

            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }

        $rtn['result'] = true;
        $rtn['affected_row'] = $this->db->affectedRows();


        // Delete customer date
        $sql = "
            DELETE FROM promotion_customer 
            WHERE pm_id IN :pm_ids: 
            ;
        ";
        $query = $this->db->query($sql, $sql_params);
        $error = $this->error();
        if ($error['code'] !== 0) {
            // Transactions - rollback
            $this->db->transRollback();

            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }


        if ($this->db->transStatus() === false) {
            // Transactions - rollback
            $this->db->transRollback();
            $rtn['result'] = false;
            $rtn['message'] = 'Transactions fail.';
        } else {
            // Transactions - commit
            $this->db->transCommit();
        }


        return $rtn;
    }

    /**
     * Insert promotion data
     *
     * @param array $data
     * @return array
     */
    public function insertDataBulk(array $data = []): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);

        // Create builder
        $builder = $this->db->table('promotion');

        $query = $builder->ignore(true)->insertBatch($data);
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }

        $rtn['result'] = true;
        $rtn['affected_row'] = $this->db->affectedRows();

        return $rtn;
    }


    /**
     * Get KW product info
     *
     * @param string $pm_id
     * @return array
     */
    public function getKwProductInfo(int $pm_id): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'list' => [], 'total_rows' => 0);


        // Get list
        $sql_params = [
            'pm_id' => $pm_id
        ];
        $sql = "
            SELECT 
                kp.type, kp.items, 
                kb.bnft_code 
            FROM promotion AS k 
                JOIN promotion_product AS kp ON k.pm_code = kp.pm_code AND k.bnft_price = kp.bnft_price AND kp.status = 1 
                JOIN promotion_benefit AS kb ON kp.bnft_price = kb.bnft_price 
            WHERE k.id = :pm_id: 
            ORDER BY kp.type ASC 
            ;
        ";
        $query = $this->query($sql, $sql_params);

        $rtn['list'] = $query->getResultArray();
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }
        $query->freeResult();


        $rtn['result'] = true;

        return $rtn;
    }

    /**
     * Get KW product info
     *
     * @param array $params
     * @param boolean $first_only
     * @return array
     */
    public function setKwProductSelect(array $params, bool $first_only = true): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);


        // Set product select
        $sql_params = $params;

        $sql_set = '';
        // SQL set - hopes
        if (!empty($params['hope_1'])) {
            $sql_set .= "kc.hope_1 = :hope_1:, ";
        } else {
            $sql_set .= "kc.hope_1 = NULL, ";
        }
        if (!empty($params['hope_2'])) {
            $sql_set .= "kc.hope_2 = :hope_2:, ";
        } else {
            $sql_set .= "kc.hope_2 = NULL, ";
        }
        if (!empty($params['hope_3'])) {
            $sql_set .= "kc.hope_3 = :hope_3:, ";
        } else {
            $sql_set .= "kc.hope_3 = NULL, ";
        }

        // SQL where
        $sql_where = '';
        if ($first_only) {
            $sql_where .= "AND kc.product_id IS NULL ";
        }

        $sql = "
            UPDATE promotion_customer AS kc 
                JOIN (
                    SELECT k.id, kp.id AS product_id 
                    FROM promotion AS k 
                        LEFT JOIN promotion_product AS kp ON k.pm_code = kp.pm_code 
                            AND k.bnft_price = kp.bnft_price 
                            AND kp.status = 1 
                            AND kp.type = :type: 
                    WHERE k.cus_name = :cus_name: AND k.cus_mobile = :cus_mobile: AND k.status = 1 
                    ORDER BY k.id DESC 
                    LIMIT 1 
                ) AS s ON kc.pm_id = s.id
            SET {$sql_set}
                kc.cus_zip = :cus_zip:, 
                kc.cus_addr1 = :cus_addr1:, 
                kc.cus_addr2 = :cus_addr2:, 
                kc.product_id = s.product_id, 
                kc.select_at = NOW() 
            WHERE TRUE 
                {$sql_where}
            ;
        ";
        $query = $this->query($sql, $sql_params);

        $rtn['list'] = $query->getResultArray();
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }

        $rtn['result'] = true;
        $rtn['affected_row'] = $this->db->affectedRows();

        return $rtn;
    }


    /**
     * Create promotion user data
     *
     * @return array
     */
    public function createCustomer(): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);


        // Transactions - start
        $this->db->transBegin();


        // Create customer date
        $sql = "
            INSERT IGNORE INTO promotion_customer(pm_id, bnft_code) 
            SELECT k.id AS pm_id, kb.bnft_code 
            FROM promotion AS k 
                JOIN promotion_benefit AS kb ON k.bnft_price = kb.bnft_price 
            WHERE k.status = 0 
            ORDER BY k.id ASC 
            ;
        ";
        $query = $this->db->query($sql);
        $error = $this->error();
        if ($error['code'] !== 0) {
            // Transactions - rollback
            $this->db->transRollback();

            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }

        $rtn['result'] = true;
        $rtn['affected_row'] = $this->db->affectedRows();


        // Set kw state
        $sql = "
            UPDATE promotion AS k 
                JOIN promotion_customer AS kc ON k.id = kc.pm_id 
            SET k.status = 1 
            WHERE k.status = 0 
            ;
        ";
        $query = $this->db->query($sql);
        if ($error['code'] !== 0) {
            // Transactions - rollback
            $this->db->transRollback();

            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }


        if ($this->db->transStatus() === false) {
            // Transactions - rollback
            $this->db->transRollback();
        } else {
            // Transactions - commit
            $this->db->transCommit();
        }


        return $rtn;
    }

    /**
     * Get KW Customer List for send sms
     *
     * @return array
     */
    public function getSmsTarget(): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'list' => [], 'total_rows' => 0);


        // Get list
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                k.cus_name, k.cus_mobile, 
                kc.id AS customer_id 
            FROM promotion_customer AS kc 
                JOIN promotion AS k ON kc.pm_id = k.id 
            WHERE kc.send_sms = 0 
            LIMIT 100 
            ;
        ";
        $query = $this->query($sql);

        $rtn['list'] = $query->getResultArray();
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }
        $query->freeResult();


        // Get total count
        $query = $this->query("SELECT FOUND_ROWS() AS total_rows; ");
        $rtn['total_rows'] = $query->getRowArray()['total_rows'];
        $query->freeResult();


        $rtn['result'] = true;

        return $rtn;
    }

    /**
     * Set Kw promotion customer to sms sended
     *
     * @return array
     */
    public function setCustomerSmsSended(int $customer_id): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);


        // Get list
        $sql_params = [
            'customer_id' => $customer_id
        ];

        $sql = "
            UPDATE promotion_customer AS ks 
            SET ks.send_sms = 1 
            WHERE ks.id = :customer_id: AND ks.send_sms = 0 
            ;
        ";
        $query = $this->db->query($sql, $sql_params);
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }

        $rtn['result'] = true;
        $rtn['affected_row'] = $this->db->affectedRows();


        return $rtn;
    }


    /**
     * Get promotion customer info
     *
     * @param string $cus_name
     * @param string $cus_mobile
     * @return array
     */
    public function getCustomerInfo(string $cus_name, string $cus_mobile): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'row' => [], 'total_rows' => 0);


        // Get list
        $sql_params = [
            'cus_name' => $cus_name,
            'cus_mobile' => $cus_mobile
        ];
        $sql = "
            SELECT 
                k.id AS pm_id, k.pm_code, k.bnft_price, 
                kc.cus_zip AS customer_zip, kc.cus_addr1 AS customer_addr1, kc.cus_addr2 AS customer_addr2, kc.hope_1, kc.hope_2, kc.hope_3, 
                kp.type, kp.items, 
                CASE kp.type 
                    WHEN 1 THEN '옵션 1' 
                    WHEN 2 THEN '옵션 2' 
                    WHEN 3 THEN '옵션 3' 
                    ELSE '-' 
                END AS type_kr 
            FROM promotion AS k 
                LEFT JOIN promotion_customer AS kc ON k.id = kc.pm_id 
                LEFT JOIN promotion_product AS kp ON kc.product_id = kp.id AND kp.status = 1
            WHERE k.cus_name = :cus_name: AND k.cus_mobile = :cus_mobile: AND k.status = 1 
            ORDER BY k.id DESC 
            LIMIT 1 
            ;
        ";
        $query = $this->query($sql, $sql_params);

        $rtn['row'] = $query->getRowArray();
        $error = $this->error();
        if ($error['code'] !== 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }
        $query->freeResult();


        $rtn['result'] = true;

        return $rtn;
    }
}