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
                kp.type, kp.items, kp.img 
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


        // Get list
        $sql_params = [
            'kw_ids' => $kw_ids
        ];

        $sql = "
            UPDATE kcar_kw AS k 
            SET k.status = 0 
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
                kp.type, kp.items, 
                kb.bnft_code 
            FROM kcar_kw_product AS kp 
                JOIN kcar_kw_benefit AS kb ON kp.bnft_price = kb.bnft_price 
            WHERE kp.kw_code = :kw_code: AND kp.bnft_price = :bnft_price: AND kp.status = 1 
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

    
}