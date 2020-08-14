<?php namespace App\Controllers\Api\Admin;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

/**
 * Prom 관련 API 컨트롤러
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
        $this->base_controller_cfg['auto_login_check'] = true;

        // Load models
        $this->prom_model = new \App\Models\PromModel();
    }

    /**
     * Get promotion list API
     *
     * @return void
     */
    public function list()
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
                'search_key' => 'in_list[pm_code,car_number,car_model,cus_name,cus_mobile,cus_zip,cus_addr1,cus_addr2,bnft_price,bnft_code,product]',
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
                'total_rows' => 0,
                'list' => []
            ]
        ];

        // Get promotion list
        $res = $this->prom_model->getPromList($params);
        if ($res['result']) {
            $rtn['data']['list'] = $res['list'];
            $rtn['data']['total_pages'] = (int)ceil($res['total_rows'] / $params['page_size']);
            $rtn['data']['total_rows'] = (int)$res['total_rows'];
        } else {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];
        }

        return $this->respond($rtn, 200, '');
    }

    /**
     * Get promotion product info API
     *
     * @param string $pm_id
     * @return void
     */
    public function productInfo(string $pm_id)
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateVariable($pm_id, 'required|is_natural_no_zero');

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'bnft_code' => [ 'items' => '', 'img' => '' ],
                'wash_service' => [ 'items' => '', 'img' => '' ],
                'wash_goods' => [ 'items' => '', 'img' => '' ],
                'car_goods' => [ 'items' => '', 'img' => '' ]
            ]
        ];

        // Get promotion product info
        $res = $this->prom_model->getKwProductInfo($pm_id);
        if ($res['result']) {
            foreach ($res['list'] as $item) {
                $rtn['data']['bnft_code'] = $item['bnft_code'];
                switch ($item['type']) {
                    case '1':
                        $rtn['data']['wash_service']['items'] = $item['items'];
                        $rtn['data']['wash_service']['img'] = $item['img'];
                        break;
                    case '2':
                        $rtn['data']['wash_goods']['items'] = $item['items'];
                        $rtn['data']['wash_goods']['img'] = $item['img'];
                        break;
                    case '3':
                        $rtn['data']['car_goods']['items'] = $item['items'];
                        $rtn['data']['car_goods']['img'] = $item['img'];
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
     * Get promotion select info API
     *
     * @param string $pm_id
     * @return void
     */
    public function SelectInfo(string $pm_id)
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateVariable($pm_id, 'required|is_natural_no_zero');

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'cus_name' => '',
                'cus_mobile' => '',
                // 'cus_zip' => '',
                // 'cus_addr1' => '',
                // 'cus_addr2' => '',
                'bnft_code' => '',
                'customer_zip' => '',
                'customer_addr1' => '',
                'customer_addr2' => '',
                'product_type' => '',
                'product_type_kr' => '',
                'product_items' => '',
                'hope_1' => '',
                'hope_2' => '',
                'hope_3' => '',
                'enable_p1' => '',
                'enable_p2' => '',
                'enable_p3' => '',
            ]
        ];

        // Get promotion product info
        $res = $this->prom_model->getKwSelectInfo($pm_id);
        if ($res['result']) {
            $item = $res['row'];
            $rtn['data']['cus_name'] = $item['cus_name'];
            $rtn['data']['cus_mobile'] = $item['cus_mobile'];
            // $rtn['data']['cus_zip'] = $item['cus_zip'];
            // $rtn['data']['cus_addr1'] = $item['cus_addr1'];
            // $rtn['data']['cus_addr2'] = $item['cus_addr2'];
            $rtn['data']['bnft_code'] = $item['bnft_code'];
            $rtn['data']['customer_zip'] = $item['customer_zip'];
            $rtn['data']['customer_addr1'] = $item['customer_addr1'];
            $rtn['data']['customer_addr2'] = $item['customer_addr2'];
            $rtn['data']['product_type'] = $item['product_type'];
            $rtn['data']['product_type_kr'] = $item['product_type_kr'];
            $rtn['data']['product_items'] = $item['product_items'];
            $rtn['data']['hope_1'] = $item['hope_1'];
            $rtn['data']['hope_2'] = $item['hope_2'];
            $rtn['data']['hope_3'] = $item['hope_3'];
            $rtn['data']['enable_p1'] = $item['enable_p1'];
            $rtn['data']['enable_p2'] = $item['enable_p2'];
            $rtn['data']['enable_p3'] = $item['enable_p3'];
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
     * Import promotion data from excel
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
        // promotion data
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
                        $rtn['message'] = '엑셀 양식 변경이 감지되었습니다.';

                        // box/spout - Close file
                        $reader->close();

                        return $this->respond($rtn, 200, '');
                    }

                    $is_first_row = false;
                    continue;
                }

                // Read and add promotion data
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
        $rtn['data']['affected_row'] += $res_reg['affected_row'] ?? 0;
        $rtn['data']['dupli_row'] += $res_reg['dupli_row'] ?? 0;

        unset($kw_data, $res_reg);

        // Send sms to new promotion customer
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
            'pm_number' => $cells[1]->getValue(), // 고유번호
            'pm_code' => $cells[2]->getValue(), // 등급코드
            // 'kw_price' => $cells[3]->getValue(), // 상품금액
            // 'kw_branch' => $cells[4]->getValue(), // 발급지점
            // 'kw_date' => $cells[5]->getValue(), // 발급일자
            // 'kw_type' => $cells[6]->getValue(), // 구분
            // 'car_number' => $cells[7]->getValue(), // 차량번호
            // 'car_manufacturer' => $cells[8]->getValue(), // 제조사
            // 'car_model' => $cells[9]->getValue(), // 모델
            // 'car_trim' => $cells[10]->getValue(), // 대분류
            // 'car_level' => $cells[11]->getValue(), // 소분류
            // 'car_year' => $cells[12]->getValue(), // 연식
            'cus_name' => $cells[13]->getValue(), // 고객
            'cus_mobile' => $cells[14]->getValue(), // 연락처
            // 'cus_zip' => $cells[15]->getValue(), // 우편번호
            // 'cus_addr1' => $cells[16]->getValue(), // 주소
            // 'cus_addr2' => $cells[17]->getValue(), // 상세주소
            'bnft_price' => $cells[18]->getValue(), // 상품권금액
            // 'bnft_type' => $cells[19]->getValue(), // 상품권종류
            // 'wash_service' => $cells[20]->getValue(), // 세차 서비스 허용 여부
        ];

        // 데이터 검증
        if (!$this->validation->check($temp['pm_number'], 'required')) {
            $rtn['message'] = '고유번호 값이 잘못 되었습니다.';
            return $rtn;
        }
        if (!$this->validation->check($temp['pm_code'], 'required|in_list[KW6,KW12]')) {
            $rtn['message'] = '등급코드 값이 잘못 되었습니다.';
            return $rtn;
        }
        // if (!$this->validation->check($temp['kw_branch'], 'required')) {
        //     $rtn['message'] = '발급지점 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['kw_date'], 'required|valid_date')) {
        //     $rtn['message'] = '발급일자 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['kw_type'], 'required')) {
        //     $rtn['message'] = '구분 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['car_number'], 'required')) {
        //     $rtn['message'] = '차량번호 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['car_manufacturer'], 'required')) {
        //     $rtn['message'] = '제조사 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['car_model'], 'required')) {
        //     $rtn['message'] = '모델 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['car_trim'], 'required')) {
        //     $rtn['message'] = '대분류 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['car_level'], '')) {
        //     $rtn['message'] = '소분류 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['car_year'], 'valid_date')) {
        //     $rtn['message'] = '연식 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        if (!$this->validation->check($temp['cus_name'], 'required')) {
            $rtn['message'] = '고객 값이 잘못 되었습니다.';
            return $rtn;
        }
        if (!$this->validation->check($temp['cus_mobile'], 'required')) {
            $rtn['message'] = '연락처 값이 잘못 되었습니다.';
            return $rtn;
        }
        if (!$this->validation->check($temp['bnft_price'], 'required|is_natural|in_list[20000,25000,30000,35000,45000,60000,50000,75000,70000,95000]')) {
            $rtn['message'] = '상품권금액 값이 잘못 되었습니다.';
            return $rtn;
        }
        // if (!$this->validation->check($temp['bnft_type'], 'required')) {
        //     $rtn['message'] = '상품권종류 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
        // if (!$this->validation->check($temp['wash_service'], 'in_list[,x,X]')) {
        //     $rtn['message'] = '세차 서비스 허용 여부 값이 잘못 되었습니다.';
        //     return $rtn;
        // }
            
        // 데이터 보정
        // if (empty($temp['wash_service'])) {
        //     $temp['wash_service'] = 1;
        // } else {
        //     $temp['wash_service'] = 0;
        // }
        // $temp['kw_date'] = date('Y-m-d', strtotime($temp['kw_date']));
        // $temp['car_year'] = date('Y-m-d', strtotime($temp['car_year']));

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

        $res = $this->prom_model->insertKwBulk($rows);
        $rtn['result'] = $res['result'];
        $rtn['message'] = $res['message'];
        $rtn['affected_row'] = $res['affected_row'];
        $rtn['dupli_row'] = count($rows) - $res['affected_row'];

        return $rtn;
    }
    /**
     * Send sms to new promotion customer
     *
     * @return array
     */
    private function _sendSms(): array
    {
        $rtn = [ 'result' => true, 'message' => '' ];

        // Create promotion user data
        $res_create = $this->prom_model->createCustomer();

        // 무한 루프 방지 하기 위해 반복 횟수 제한
        $do_cunter = 500;
        do {
            // Get promotion Customer List in 100 units for send sms
            $res_customer = $this->prom_model->getSmsTarget();
            if (!$res_customer['result']) {
                $rtn['result'] = false;
                $rtn['message'] = $res_customer['message'];
                return $rtn;
            }

            // Send SMS progress
            foreach ($res_customer['list'] as $item) {
                // Set promotion customer to sms sended
                $res_send = $this->prom_model->setCustomerSmsSended($item['customer_id']);
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
                $sms_res = $this->_sendSmsToCustomer($item['cus_mobile']);
                if (!$sms_res['result']) {
                    $rtn['result'] = false;
                    $rtn['message'] = $sms_res['message'];

                    // Set Kw promotion customer to sms fail
                    $res_fail = $this->prom_model->setCustomerSmsFail($item['customer_id']);
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
     * Delete promotion data
     *
     * @return mixed
     */
    private function _kwDelete()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'DELETE' ]);

        // Read parameters
        $pm_ids = $this->commonLib->readRawInput('pm_ids', []);

        // Validate parameter
        $this->validateVariable($pm_ids, 'required');

        // Additional validate parameter
        foreach ($pm_ids as $item) {
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
        
        // Get promotion info
        $res = $this->prom_model->deleteKw($pm_ids);
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
     * @return void
     */
    public function sms()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'POST' ]);

        // Validate parameter
        $this->validateParameter([
            'pm_id' => 'required|is_natural_no_zero',
        ]);

        // Read parameters
        $pm_id = $this->commonLib->readPostGet('pm_id', 0);

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => []
        ];

        // Get promotion info
        $res = $this->prom_model->getKwInfo($pm_id);
        if (!$res['result'] || empty($res['row'])) {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];

            return $this->respond($rtn, 200, '');
        }

        // Send SMS
        $sms_res = $this->_sendSmsToCustomer($res['row']['cus_mobile']);
        if (!$sms_res['result']) {
            $rtn['result'] = false;
            $rtn['message'] = $sms_res['message'];
        }


        return $this->respond($rtn, 200, '');
    }


    /**
     * Send sms to customer
     *
     * @param string $sms_to
     * @return array
     */
    private function _sendSmsToCustomer(string $sms_to): array
    {
        $sms_title = 'K Car Warranty 보증만료 고객 혜택 안내';
        $sms_content = "고객님, 안녕하세요.\nK Car 고객서비스팀 입니다.\n\nK Car Warranty 상품을 구매해주신 고객분들 중 제조사 일반보증 잔존 차량에 한 해 감사의 뜻으로 프리미엄 서비스를 무료로 제공해드리고 있습니다.\n\n하단의 URL을 통해 서비스 신청 부탁드립니다.\n\n■ 서비스 상품 보러 가기: http://kcar.autocarz.co.kr/kcar/kw\n\n■ 문의 사항이 있으실 경우 02-1800-0206 으로 연락 주시면 빠른 안내 가능하십니다.\n(평일 오전 9시 30분~오후 6시 30분)\n\n항상 고객님께 더 나은 서비스를 제공해 드릴 수 있도록 노력하겠습니다.\n감사합니다.\n\n즐거운 하루 되세요.^^\n\n▼▼서비스 자세히 보기▼▼\nhttps://www.autocarz.co.kr/kcar";

        return $this->commonLib->sendLms($sms_to, $sms_title, $sms_content);
    }
}