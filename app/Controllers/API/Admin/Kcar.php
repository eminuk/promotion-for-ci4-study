<?php namespace App\Controllers\API\Admin;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

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
     * Get KW select info API
     *
     * @param string $kw_code
     * @param string $bnft_price
     * @return void
     */
    public function SelectInfo(string $kw_id)
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateVariable($kw_id, 'required|is_natural_no_zero');

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'bnft_code' => '',
                'product_type' => '',
                'product_type_kr' => '',
                'product_items' => '',
                'hope_1' => '',
                'hope_2' => '',
                'hope_3' => ''
            ]
        ];

        // Get KW product info
        $res = $this->_Kw_model->getKwSelectInfo($kw_id);
        if ($res['result']) {
            $item = $res['row'];
            $rtn['data']['bnft_code'] = $item['bnft_code'];
            $rtn['data']['product_type'] = $item['type'];
            $rtn['data']['product_type_kr'] = $item['type_kr'];
            $rtn['data']['product_items'] = $item['items'];
            $rtn['data']['hope_1'] = $item['hope_1'];
            $rtn['data']['hope_2'] = $item['hope_2'];
            $rtn['data']['hope_3'] = $item['hope_3'];
        } else {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];
        }

        return $this->respond($rtn, 200, '');
    }

    public function kw()
    {
        switch ($this->request->getMethod(TRUE)) {
            case 'POST':
                return $this->_kwPost();
                break;
            case 'DELETE':
                return $this->_kwDelete();
                break;
            default:
                $this->responseMethodNotAllowed();
                break;
        }
    }

    /**
     * Import KW promotion data from excel
     *
     * @return void
     */
    private function _kwPost()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'POST' ]);

        // Read files
        $file_excel = $this->commonLib->readFile('file_excel');

        // Validate parameter
        if (empty($file_excel)) {
            $this->responseParameterValidateFail([
                'file_excel' => 'The file is required.'
            ]);
        }

        // Validate file
        if (!$file_excel->isValid()) {
            $this->responseParameterValidateFail([
                'file_excel' => $file_excel->getErrorString().'('.$file_excel->getError().')'
            ]);
        }

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'affected_row' => 0,
                'dupli_row' =>0
            ]
        ];

        // box/spout - Create reder and open file
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader
            ->setShouldFormatDates(true)
            ->open($file_excel->getTempName());

        // Check header row
        $is_first_row = true;
        // KW promotion data
        $kw_data = [];

        // box/spout - Read sheets
        foreach ($reader->getSheetIterator() as $sheet) {
            // box/spout - Read sheet's row
            foreach ($sheet->getRowIterator() as $row) {
                // box/spout - Do stuff with the row
                $cells = $row->getCells();

                // Check header row
                if ($is_first_row) {
                    // Check templete
                    if (!$this->_kwPostExcelCheck($cells)) {
                        $rtn['result'] = false;
                        $rtn['message'] = '템플릿 변경이 감지되었습니다.';

                        // box/spout - Close file
                        $reader->close();

                        return $this->respond($rtn, 200, '');
                    }

                    $is_first_row = false;
                    continue;
                }

                // Read and add kw data
                $res_data = $this->_kwPostReadData($cells);
                if (!$res_data['result']) {
                    $rtn['message'] = $res_data['message'];
                    break;
                }
                $kw_data[] = $res_data['data'];
                unset($cells, $res_data);

                // Register in 200 units
                if (count($kw_data) > 200) {
                    // Data registration
                    $res_reg = $this->_kwPostRegData($kw_data);
                    if (!$res_reg['result']) {
                        // $rtn['result'] = false;
                        $rtn['message'] .= "\n{$res_reg['message']}";

                        unset($kw_data, $res_reg);
                        break;
                    }

                    // Add count
                    $rtn['data']['affected_row'] += $res_reg['affected_row'];
                    $rtn['data']['dupli_row'] += $res_reg['dupli_row'];

                    unset($kw_data, $res_reg);
                    $kw_data = [];
                }
            }
            // Read only first sheet
            break;
        }

        // box/spout - Close file
        $reader->close();

        // Register
        if (count($kw_data) > 0) {
            // Data registration
            $res_reg = $this->_kwPostRegData($kw_data);
            if (!$res_reg['result']) {
                // $rtn['result'] = false;
                $rtn['message'] .= "\n{$res_reg['message']}";
            }
        }

        // Add count
        $rtn['data']['affected_row'] += $res_reg['affected_row'];
        $rtn['data']['dupli_row'] += $res_reg['dupli_row'];

        unset($kw_data, $res_reg);

        // Send sms to new KW customer
        $res_sms = $this->_sendSms();

        return $this->respond($rtn, 200, '');
    }
    /**
     * Check templete
     *
     * @param array $cells
     * @return bool
     */
    private function _kwPostExcelCheck(array $cells): bool
    {
        if ($cells[1]->getValue() !== '보증번호') {
            return false;
        }
        if ($cells[2]->getValue() !== '상품') {
            return false;
        }
        if ($cells[3]->getValue() !== '상품금액') {
            return false;
        }
        if ($cells[4]->getValue() !== '발급지점') {
            return false;
        }
        if ($cells[7]->getValue() !== '차량번호') {
            return false;
        }
        if ($cells[8]->getValue() !== '제조사') {
            return false;
        }
        if ($cells[9]->getValue() !== '모델') {
            return false;
        }
        if ($cells[13]->getValue() !== '고객') {
            return false;
        }
        if ($cells[14]->getValue() !== '연락처') {
            return false;
        }
        if ($cells[15]->getValue() !== '우편번호') {
            return false;
        }
        if ($cells[16]->getValue() !== '주소') {
            return false;
        }
        if ($cells[17]->getValue() !== '상세주소') {
            return false;
        }
        if ($cells[18]->getValue() !== '상품권금액') {
            return false;
        }

        return true;
    }
    /**
     * Read cell's data
     *
     * @param array $cells
     * @return array
     */
    private function _kwPostReadData(array $cells): array
    {
        $rtn = [ 'result' => false, 'message' => '', 'data' => [] ];

        $temp = [
            'kw_number' => $cells[1]->getValue(), // 보증번호
            'kw_code' => $cells[2]->getValue(), // 상품
            'kw_price' => $cells[3]->getValue(), // 상품금액
            'kw_branch' => $cells[4]->getValue(), // 발급지점
            'car_number' => $cells[7]->getValue(), // 차량번호
            'car_manufacturer' => $cells[8]->getValue(), // 제조사
            'car_model' => $cells[9]->getValue(), // 모델
            'cus_name' => $cells[13]->getValue(), // 고객
            'cus_mobile' => $cells[14]->getValue(), // 연락처
            'cus_zip' => $cells[15]->getValue(), // 우편번호
            'cus_addr1' => $cells[16]->getValue(), // 주소
            'cus_addr2' => $cells[17]->getValue(), // 상세주소
            'bnft_price' => $cells[18]->getValue(), // 상품권금액
        ];

        // 데이터 검증
        if (!$this->validation->check($temp['kw_number'], 'required')) {
            $rtn['message'] = '보증번호 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['kw_code'], 'required')) {
            $rtn['message'] = '상품 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['kw_branch'], 'required')) {
            $rtn['message'] = '발급지점 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['car_number'], 'required')) {
            $rtn['message'] = '차량번호 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['car_manufacturer'], 'required')) {
            $rtn['message'] = '제조사 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['car_model'], 'required')) {
            $rtn['message'] = '모델 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['cus_name'], 'required')) {
            $rtn['message'] = '고객 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['cus_mobile'], 'required')) {
            $rtn['message'] = '연락처 값이 잘못 되었습니다. - required';
            return $rtn;
        }
        if (!$this->validation->check($temp['bnft_price'], 'required|is_natural')) {
            $rtn['message'] = '상품권금액 값이 잘못 되었습니다. - required|is_natural';
            return $rtn;
        }

        // Set respons array
        $rtn['result'] = true;
        $rtn['data'] = $temp;

        return $rtn;
    }
    /**
     * Data registration
     *
     * @param array $rows
     * @return array
     */
    private function _kwPostRegData(array $rows): array
    {
        $rtn = [ 'result' => true, 'message' => '', 'affected_row' => 0, 'dupli_row' => 0 ];

        $res = $this->_Kw_model->insertKwBulk($rows);
        $rtn['result'] = $res['result'];
        $rtn['message'] = $res['message'];
        $rtn['affected_row'] = $res['affected_row'];
        $rtn['dupli_row'] = count($rows) - $res['affected_row'];

        return $rtn;
    }
    /**
     * Send sms to new KW customer
     *
     * @return array
     */
    private function _sendSms(): array
    {
        $rtn = [ 'result' => true, 'message' => '' ];

        // Create Kw promotion user data
        $res_create = $this->_Kw_model->createCustomer();

        // 무한 루프 방지 하기 위해 반복 횟수 제한
        $do_cunter = 500;
        do {
            // Get KW Customer List in 100 units for send sms
            $res_customer = $this->_Kw_model->getSmsTarget();
            if (!$res_customer['result']) {
                $rtn['result'] = false;
                $rtn['message'] = $res_customer['message'];
                return $rtn;
            }

            // Send SMS progress
            foreach ($res_customer['list'] as $item) {
                // Set Kw promotion customer to sms sended
                $res_send = $this->_Kw_model->setCustomerSmsSended($item['customer_id']);
                if (!$res_send['result']) {
                    $rtn['result'] = false;
                    $rtn['message'] = $res_send['message'];
                    return $rtn;
                }

                // Check sms state
                if ($res_send['affected_row'] == 0) {
                    continue;
                }

                // Send SMS
                $sms_to = $item['cus_mobile'];
                $sms_title = '타이틀 필요';
                $sms_content = '템플릿 필요';
                $sms_res = $this->commonLib->sendLms($sms_to, $sms_title, $sms_content);
                if (!$sms_res['result']) {
                    $rtn['result'] = false;
                    $rtn['message'] = $sms_res['message'];

                    // Set Kw promotion customer to sms fail
                    $res_fail = $this->_Kw_model->setCustomerSmsFail($item['customer_id']);
                    if (!$res_fail['result']) {
                        $rtn['message'] .= $res_fail['message'];
                    }

                    return $rtn;
                }

                unset($res_send, $sms_res);
            }

            unset($res_customer);
        } while ($do_cunter-- > 0);


        return $rtn;
    }

    /**
     * Delete KW promotion data
     *
     * @return mixed
     */
    private function _kwDelete()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'DELETE' ]);

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