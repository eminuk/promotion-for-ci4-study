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
        $this->base_controller_cfg['auto_login_check'] = true;

        // Load models
        $this->_Kw_model = new \App\Models\Admin\KwModel();
    }

    /**
     * Get KW list API
     *
     * @return void
     */
    public function kwList()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateParameter([
            'is_select' => 'required|in_list[ALL,Y,N]',
            // 'sdate' => '',
            // 'edate' => '',
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
        if (!empty($params['sdate'])) {
            // Validate parameter
            $this->validateParameter([
                'sdate' => 'valid_date',
            ]);
        }
        if (!empty($params['edate'])) {
            // Validate parameter
            $this->validateParameter([
                'edate' => 'valid_date',
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
        if ($res['result']) {
            $rtn['data']['list'] = $res['list'];
            $rtn['data']['total_pages'] = (int)ceil($res['total_rows'] / $params['page_size']);
        } else {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];
        }

        return $this->respond($rtn, 200, '');
    }

    /**
     * Get KW product info API
     *
     * @param string $kw_code
     * @param string $bnft_price
     * @return void
     */
    public function productInfo(string $kw_code, string $bnft_price)
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateVariable($kw_code, 'required');
        $this->validateVariable($bnft_price, 'required|is_natural_no_zero');

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'bnft_code' => '',
                'wash_service' => '',
                'wash_goods' => '',
                'car_goods' => ''
            ]
        ];

        // Get KW product info
        $res = $this->_Kw_model->getKwProductInfo($kw_code, $bnft_price);
        if ($res['result']) {
            foreach ($res['list'] as $item) {
                $rtn['data']['bnft_code'] = $item['bnft_code'];
                switch ($item['type']) {
                    case '1':
                        $rtn['data']['wash_service'] = $item['items'];
                        break;
                    case '2':
                        $rtn['data']['wash_goods'] = $item['items'];
                        break;
                    case '3':
                        $rtn['data']['car_goods'] = $item['items'];
                        break;
                    default:
                        break;
                }
            }
        } else {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];
        }

        return $this->respond($rtn, 200, '');
    }

    /**
     * Send sms
     *
     * @param string $kw_code
     * @param string $bnft_price
     * @return void
     */
    public function kw()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'DELETE', 'GET' ]);

        // Read parameters
        $kw_ids = $this->commonLib->readRawInput('kw_ids', []);

        // Validate parameter
        $this->validateVariable($kw_ids, 'required');

        // Additional validate parameter
        foreach ($kw_ids as $item) {
            $this->validateVariable($item, 'is_natural_no_zero');
        }
        
        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'affected_row' => 0
            ]
        ];
        
        // Get Kw promotion info
        $res = $this->_Kw_model->deleteKw($kw_ids);
        if (!$res['result']) {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];

            return $this->respond($rtn, 200, '');
        }

        $rtn['data']['affected_row'] = $res['affected_row'];

        return $this->respond($rtn, 200, '');
    }


    /**
     * Send sms
     *
     * @param string $kw_code
     * @param string $bnft_price
     * @return void
     */
    public function sms()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'POST' ]);

        // Validate parameter
        $this->validateParameter([
            'kw_id' => 'required|is_natural_no_zero',
        ]);

        // Read parameters
        $kw_id = $this->commonLib->readPostGet('kw_id', 0);

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => []
        ];

        // Get Kw promotion info
        $res = $this->_Kw_model->getKwInfo($kw_id);
        if (!$res['result'] || empty($res['row'])) {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];

            return $this->respond($rtn, 200, '');
        }

        // Send SMS
        $sms_to = $res['row']['cus_mobile'];
        $sms_title = '타이틀 필요';
        $sms_content = '템플릿 필요';
        $sms_res = $this->commonLib->sendLms($sms_to, $sms_title, $sms_content);
        if (!$sms_res['result']) {
            $rtn['result'] = false;
            $rtn['message'] = $sms_res['message'];
        }


        return $this->respond($rtn, 200, '');
    }
}