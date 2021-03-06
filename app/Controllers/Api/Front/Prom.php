<?php namespace App\Controllers\Api\Front;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

/**
 * Promotion 관련 API 컨트롤러
 */
class Prom extends \App\Controllers\Api\BaseController
{
    /**
     * Prom model
     *
     * @var [type]
     */
    private $prom_model;

    /**
     * Construct
     */
    public function __construct()
    {
        // Set base controller config
        $this->base_controller_cfg['auto_login_check'] = false;

        // Load models
        $this->prom_model = new \App\Models\PromModel();
    }


    /**
     * Get customer Info API
     *
     * @return void
     */
    public function cuatomer()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateParameter([
            'cus_name' => 'required|max_length[6]',
            'cus_mobile' => 'required|max_length[11]|is_natural',
        ]);

        // Read parameters
        $params = [
            'cus_name' => $this->commonLib->readPostGet('cus_name'),
            'cus_mobile' => $this->commonLib->readPostGet('cus_mobile'),
        ];

        // Additional validate parameter
        if (!$this->commonLib->isMobileNum($params['cus_mobile'])) {
            $this->responseParameterValidateFail([
                'cus_mobile' => 'The cus_mobile must be mobile number pattern.'
            ]);
        }

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'is_customer' => false
            ]
        ];

        // Get customer info
        $res = $this->prom_model->getCustomerInfo($params['cus_name'], $params['cus_mobile']);
        if ($res['result']) {
            $rtn['data']['is_customer'] = !empty($res['row']);
        } else {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];
        }

        return $this->respond($rtn, 200, '');
    }

    /**
     * Selete promotion product API
     *
     * @return void
     */
    public function product()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'PUT' ]);

        // Read parameters
        $params = [
            'cus_name' => $this->commonLib->readRawInput('cus_name'),
            'cus_mobile' => $this->commonLib->readRawInput('cus_mobile'),
            'type' => $this->commonLib->readRawInput('type'),
            'cus_zip' => $this->commonLib->readRawInput('cus_zip'),
            'cus_addr1' => $this->commonLib->readRawInput('cus_addr1'),
            'cus_addr2' => $this->commonLib->readRawInput('cus_addr2'),
            'hope_1' => $this->commonLib->readRawInput('hope_1'),
            'hope_2' => $this->commonLib->readRawInput('hope_2'),
            'hope_3' => $this->commonLib->readRawInput('hope_3'),
            'first_only' => $this->commonLib->readRawInput('first_only', 'Y'),
        ];

        // Validate parameter
        if (!$this->validateCheck($params['cus_name'], 'required')) {
            $this->responseParameterValidateFail([
                'cus_name' => 'The cus_name is must required.'
            ]);
        }
        if (!$this->commonLib->isMobileNum($params['cus_mobile'])) {
            $this->responseParameterValidateFail([
                'cus_mobile' => 'The cus_mobile must be mobile number pattern.'
            ]);
        }
        if (!$this->validateCheck($params['type'], 'required|is_natural')) {
            $this->responseParameterValidateFail([
                'type' => 'The type is must required and is number.'
            ]);
        }
        if (!$this->validateCheck($params['cus_zip'], 'required|is_natural')) {
            $this->responseParameterValidateFail([
                'cus_zip' => 'The cus_zip is must required and is number.'
            ]);
        }
        if (!$this->validateCheck($params['cus_addr1'], 'required')) {
            $this->responseParameterValidateFail([
                'cus_addr1' => 'The cus_addr1 is must required.'
            ]);
        }
        if (!$this->validateCheck($params['first_only'], 'in_list[,Y,N]')) {
            $this->responseParameterValidateFail([
                'first_only' => 'The first_only is in_list[,Y,N]'
            ]);
        }

        // Additional validate parameter
        if (!empty($params['hope_1']) && !$this->validateCheck($params['hope_1'], 'valid_date')) {
            $this->responseParameterValidateFail([
                'hope_1' => 'The hope_1 is must date type.'
            ]);
        }
        if (!empty($params['hope_2']) && !$this->validateCheck($params['hope_2'], 'valid_date')) {
            $this->responseParameterValidateFail([
                'hope_2' => 'The hope_2 is must date type.'
            ]);
        }
        if (!empty($params['hope_3']) && !$this->validateCheck($params['hope_3'], 'valid_date')) {
            $this->responseParameterValidateFail([
                'hope_3' => 'The hope_3 is must date type.'
            ]);
        }
        if (empty($params['hope_1']) && empty($params['hope_2']) && empty($params['hope_3'])) {
            $this->responseParameterValidateFail([
                'hope' => 'The hope is required.'
            ]);
        }


        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'affected_row' => 0
            ]
        ];

        // Set product setect
        $res = $this->prom_model->setProductSelect($params, in_array($params['first_only'], ['Y', 'y']));
        if ($res['result']) {
            $rtn['data']['affected_row'] = $res['affected_row'];
        } else {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];
        }

        return $this->respond($rtn, 200, '');
    }


}