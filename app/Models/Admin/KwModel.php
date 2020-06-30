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
     * @return array
     */
    public function getKwList(array $params = []): array
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
        $sql_limit = "LIMIT ".$this->db->escapeString($offset).", ".$this->db->escapeString($params['page_size'])." ";

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                k.id, k.kw_code, k.kw_price, k.kw_branch, k.car_number, k.car_manufacturer, k.car_model, 
                k.cus_name, k.cus_mobile, k.bnft_price, 
                kc.bnft_code, IF(kc.product_id IS NULL, 'N', 'Y') AS is_select, kc.select_at, 
                kc.cus_zip, kc.cus_addr1, kc.cus_addr2, kc.send_sms, 
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
        if ($error['code'] == 0) {
            $rtn['result'] = false;
            $rtn['message'] = $error['message'];
            return $rtn;
        }
        $query->freeResult();


        // Get total count
        $query = $this->query('SELECT FOUND_ROWS() AS total_rows; ');
        $rtn['total_rows'] = $query->getRowArray()['total_rows'];
        $query->freeResult();


        $rtn['result'] = true;

        return $rtn;
    }
}