<?php namespace App\Controllers\API\Admin;


/**
 * Kcar 관련 API 컨트롤러
 */
class Kcar extends \App\Controllers\API\BaseController
{
    /**
     * Kw model
     *
     * @var [type]
     */
    private $_Kw_model;

    /**
     * Construct
     */
    public function __construct()
    {
        // Set base controller config
        $this->base_controller_cfg['auto_login_check'] = false;

        // Load models
        $this->_Kw_model = new \App\Models\Admin\KwModel();
    }

    public function kw_list()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateParameter([
            'is_select' => 'required|in_list[ALL,Y,N]',
            'sdate' => 'required|valid_date',
            'edate' => 'required|valid_date',
            'search_key' => 'required_with[search_value]',
            'page_size' => 'if_exist|is_natural',
            'page_num' => 'if_exist|is_natural'
        ]);

        // Read parameters
        $params = [
            'is_select' => $this->commonLib->readPostGet('is_select'),
            'sdate' => $this->commonLib->readPostGet('sdate'),
            'edate' => $this->commonLib->readPostGet('edate'),
            'search_key' => $this->commonLib->readPostGet('search_key'),
            'search_value' => $this->commonLib->readPostGet('search_value'),
            'page_size' => $this->commonLib->readPostGet('page_size', 10),
            'page_num' => $this->commonLib->readPostGet('page_num', 1),
        ];

        // Additional validate parameter
        if (!empty($params['search_value'])) {
            // Validate parameter
            $this->validateParameter([
                'search_key' => 'in_list[kw_code,car_number,car_model,cus_name,cus_mobile,cus_zip,cus_addr1,cus_addr2,bnft_price,bnft_code,product]',
            ]);
        }

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'page_size' => $params['page_size'],
                'page_num' => $params['page_num'],
                'total_pages' => 0,
                'list' => []
            ]
        ];

        // Get Kw list
        $res = $this->_Kw_model->getKwList($params);
        $rtn['data']['list'] = $res['list'];
        $rtn['data']['total_pages'] = (int)ceil($res['total_rows'] / $params['page_size']);

        return $this->respond($rtn, 200, '');
    }
}