<?php namespace App\Models\Admin;

use CodeIgniter\Model;


class KwModel extends Model
{
    // protected $DBGroup = '';
    // protected $table = '';
    // protected $primaryKey = '/';

    protected $returnType = 'App\Entities\Admin\Kw';
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
     * Get Kw promotion list
     *
     * @param array $params
     * @param boolean $use_paging
     * @return array
     */
    public function getKwList(array $params = [], bool $use_paging = true): array
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
                case 'kw_code':
                    $sql_where .= "AND kp.items LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'car_number':
                    $sql_where .= "AND k.car_number LIKE :search_value: ESCAPE '!' ";
                    $sql_params['search_value'] = '%'.$this->db->escapeString($params['search_value']).'%';
                    break;
                case 'car_model':
                    $sql_where .= "AND k.car_model LIKE :search_value: ESCAPE '!' ";
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
                case 'product':
                    $sql_where .= "AND kp.items LIKE :search_value: ESCAPE '!' ";
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
                k.id, k.kw_code, k.kw_price, k.kw_branch, k.car_number, k.car_manufacturer, k.car_model, 
                k.cus_name, k.cus_mobile, k.bnft_price, 
                kc.bnft_code, IF(kc.product_id IS NULL, '미신청', '신청') AS is_select_kr, kc.select_at, 
                kc.cus_zip, kc.cus_addr1, kc.cus_addr2, kc.send_sms, 
                CASE kc.send_sms 
                    WHEN 0 THEN '미발송' 
                    WHEN 1 THEN '발송완료' 
                    WHEN 2 THEN '발송실패' 
                    WHEN 3 THEN '재발송요청' 
                    ELSE '-' 
                END AS send_sms_kr, 
                kp.id AS product_id, kp.type, kp.items, kp.img,
                CASE kp.type 
                    WHEN 1 THEN '출장세차' 
                    WHEN 2 THEN '세차용품' 
                    WHEN 3 THEN '자동차용품' 
                    ELSE '-' 
                END AS type_kr 
            FROM kcar_kw AS k 
                JOIN kcar_kw_customer AS kc ON k.id = kc.kw_id 
                LEFT JOIN kcar_kw_product AS kp ON kc.product_id = kp.id 
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
     * @param integer $kw_id
     * @return array
     */
    public function getKwInfo(int $kw_id = 0): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'row' => []);


        // Get list
        $sql_params = [
            'kw_id' => $kw_id
        ];

        $sql = "
            SELECT 
                k.id, k.kw_code, k.kw_price, k.kw_branch, k.car_number, k.car_manufacturer, k.car_model, 
                k.cus_name, k.cus_mobile, k.bnft_price, 
                kc.bnft_code, IF(kc.product_id IS NULL, '미신청', '신청') AS is_select_kr, kc.select_at, 
                kc.cus_zip, kc.cus_addr1, kc.cus_addr2, kc.send_sms, 
                CASE kc.send_sms 
                    WHEN 0 THEN '미발송' 
                    WHEN 1 THEN '발송완료' 
                    WHEN 2 THEN '발송실패' 
                    WHEN 3 THEN '재발송요청' 
                    ELSE '-' 
                END AS send_sms_kr, 
                kp.type, kp.items, kp.img 
            FROM kcar_kw AS k 
                JOIN kcar_kw_customer AS kc ON k.id = kc.kw_id 
                LEFT JOIN kcar_kw_product AS kp ON kc.product_id = kp.id 
            WHERE k.status = 1 AND k.id = :kw_id:
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
     * Delete Kw promotion data
     *
     * @param array $kw_ids
     * @return array
     */
    public function deleteKw(array $kw_ids = []): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);


        // Set delete state
        $sql_params = [
            'kw_ids' => $kw_ids
        ];

        $sql = "
            UPDATE kcar_kw AS k 
            SET k.status = 2 
            WHERE k.id IN :kw_ids: AND k.status = 1 
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
     * Insert Kw promotion data
     *
     * @param array $kw_data
     * @return array
     */
    public function insertKwBulk(array $kw_data = []): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);

        // Create builder
        $builder = $this->db->table('kcar_kw');

        $query = $builder->ignore(true)->insertBatch($kw_data);
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
     * Get KW select info
     *
     * @param string $kw_id
     * @return array
     */
    public function getKwSelectInfo(int $kw_id): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'row' => [], 'total_rows' => 0);


        // Get list
        $sql_params = [
            'kw_id' => $kw_id
        ];

        $sql = "
            SELECT 
                kc.bnft_code, IFNULL(kc.hope_1, '') AS hope_1, IFNULL(kc.hope_2, '') AS hope_2, IFNULL(kc.hope_3, '') AS hope_3, 
                kp.type, kp.items, 
                CASE kp.type 
                    WHEN 1 THEN '출장세차' 
                    WHEN 2 THEN '세차용품' 
                    WHEN 3 THEN '자동차용품' 
                    ELSE '-' 
                END AS type_kr 
            FROM kcar_kw_customer AS kc 
                JOIN kcar_kw_product AS kp ON kc.product_id = kp.id 
            WHERE kc.kw_id = :kw_id: 
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
     * Get KW product info
     *
     * @param string $kw_code
     * @param string $bnft_price
     * @return array
     */
    public function getKwProductInfo(string $kw_code, string $bnft_price): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'list' => [], 'total_rows' => 0);


        // Get list
        $sql_params = [
            'kw_code' => $kw_code,
            'bnft_price' => $bnft_price
        ];
        $sql = "
            SELECT 
                kp.type, kp.items, kp.img, 
                kb.bnft_code 
            FROM kcar_kw_product AS kp 
                JOIN kcar_kw_benefit AS kb ON kp.bnft_price = kb.bnft_price 
            WHERE kp.kw_code = :kw_code: AND kp.bnft_price = :bnft_price: AND kp.status = 1 
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
     * @param string $kw_code
     * @param string $bnft_price
     * @return array
     */
    public function setKwProductSelect(array $params): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);


        // Set product select
        $sql_params = $params;

        $sql_set = '';
        if (!empty($params['hope_1'])) {
            $sql_set .= "kc.hope_1 = :hope_1:, ";
        }
        if (!empty($params['hope_2'])) {
            $sql_set .= "kc.hope_2 = :hope_2:, ";
        }
        if (!empty($params['hope_3'])) {
            $sql_set .= "kc.hope_3 = :hope_3:, ";
        }

        $sql = "
            UPDATE kcar_kw_customer AS kc 
                JOIN (
                    SELECT k.id, kp.id AS product_id 
                    FROM kcar_kw AS k 
                        LEFT JOIN kcar_kw_product AS kp ON k.kw_code = kp.kw_code 
                            AND k.bnft_price = kp.bnft_price 
                            AND kp.status = 1 
                            AND kp.type = :type: 
                    WHERE k.cus_name = :cus_name: AND k.cus_mobile = :cus_mobile: AND k.status = 1 
                    ORDER BY k.id DESC 
                    LIMIT 1 
                ) AS s ON kc.kw_id = s.id
            SET kc.cus_zip = :cus_zip:, 
                kc.cus_addr1 = :cus_addr1:, 
                kc.cus_addr2 = :cus_addr2:, 
                {$sql_set}
                kc.product_id = s.product_id, 
                kc.select_at = NOW() 
            WHERE kc.product_id IS NULL 
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
     * Create Kw promotion user data
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
            INSERT IGNORE INTO kcar_kw_customer(kw_id, bnft_code) 
            SELECT k.id AS kw_id, kb.bnft_code 
            FROM kcar_kw AS k 
                JOIN kcar_kw_benefit AS kb ON k.bnft_price = kb.bnft_price 
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
            UPDATE kcar_kw AS k 
                JOIN kcar_kw_customer AS kc ON k.id = kc.kw_id 
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
            FROM kcar_kw_customer AS kc 
                JOIN kcar_kw AS k ON kc.kw_id = k.id 
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
            UPDATE kcar_kw_customer AS ks 
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
     * Set Kw promotion customer to sms fail
     *
     * @return array
     */
    public function setCustomerSmsFail(int $customer_id): array
    {
        // Default return variable
        $rtn = array('result' => false, 'message' => '', 'affected_row' => 0);


        // Get list
        $sql_params = [
            'customer_id' => $customer_id
        ];

        $sql = "
            UPDATE kcar_kw_customer AS ks 
            SET ks.send_sms = 0 
            WHERE ks.id = :customer_id: AND ks.send_sms = 1 
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
     * Get KW customer info
     *
     * @param string $kw_code
     * @param string $bnft_price
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
                k.kw_code, k.bnft_price, 
                kc.cus_zip, kc.cus_addr1, kc.cus_addr2, kc.hope_1, kc.hope_2, kc.hope_3, 
                kp.type, kp.img, kp.items, 
                CASE kp.type 
                    WHEN 1 THEN '출장세차' 
                    WHEN 2 THEN '세차용품' 
                    WHEN 3 THEN '자동차용품' 
                    ELSE '-' 
                END AS type_kr 
            FROM kcar_kw AS k 
                LEFT JOIN kcar_kw_customer AS kc ON k.id = kc.kw_id 
                LEFT JOIN kcar_kw_product AS kp ON kc.product_id = kp.id AND kp.status = 1
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