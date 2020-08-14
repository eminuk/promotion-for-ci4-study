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
     * Get promotion select info API
     *
     * @param string $prom_id
     * @param string $bnft_price
     * @return void
     */
    public function SelectInfo(string $prom_id)
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateVariable($prom_id, 'required|is_natural_no_zero');

        // Set default response data
        $rtn = [
            'result' => true,
            'message' => '',
            'data' => [
                'cus_name' => '',
                'cus_mobile' => '',
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

        // Get product info
        $res = $this->prom_model->getSelectInfo($prom_id);
        if ($res['result']) {
            $item = $res['row'];
            $rtn['data']['cus_name'] = $item['cus_name'];
            $rtn['data']['cus_mobile'] = $item['cus_mobile'];
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

    /**
     * Promotion data
     *
     * @return void
     */
    public function data()
    {
        switch ($this->request->getMethod(TRUE)) {
            case 'POST':
                return $this->_dataPost();
                break;
            case 'DELETE':
                return $this->_dataDelete();
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
    private function _dataPost()
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
        $data = [];

        // box/spout - Read sheets
        foreach ($reader->getSheetIterator() as $sheet) {
            // box/spout - Read sheet's row
            foreach ($sheet->getRowIterator() as $row) {
                // box/spout - Do stuff with the row
                $cells = $row->getCells();

                // Check header row
                if ($is_first_row) {
                    // Check templete
                    if (!$this->_dataPostExcelCheck($cells)) {
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
                $res_data = $this->_dataPostRead($cells);
                if (!$res_data['result']) {
                    $rtn['message'] = $res_data['message'];
                    break;
                }
                $data[] = $res_data['data'];
                unset($cells, $res_data);

                // Register in 200 units
                if (count($data) > 200) {
                    // Data registration
                    $res_reg = $this->_dataPostReg($data);
                    if (!$res_reg['result']) {
                        // $rtn['result'] = false;
                        $rtn['message'] .= "\n{$res_reg['message']}";

                        unset($data, $res_reg);
                        break;
                    }

                    // Add count
                    $rtn['data']['affected_row'] += $res_reg['affected_row'];
                    $rtn['data']['dupli_row'] += $res_reg['dupli_row'];

                    unset($data, $res_reg);
                    $data = [];
                }
            }
            // Read only first sheet
            break;
        }

        // box/spout - Close file
        $reader->close();

        // Register
        if (count($data) > 0) {
            // Data registration
            $res_reg = $this->_dataPostReg($data);
            if (!$res_reg['result']) {
                // $rtn['result'] = false;
                $rtn['message'] .= "\n{$res_reg['message']}";
            }
        }

        // Add count
        $rtn['data']['affected_row'] += $res_reg['affected_row'] ?? 0;
        $rtn['data']['dupli_row'] += $res_reg['dupli_row'] ?? 0;

        unset($data, $res_reg);

        // Create customer data
        $res_sms = $this->_dataPostCustomer();

        return $this->respond($rtn, 200, '');
    }
    /**
     * Check templete
     *
     * @param array $cells
     * @return bool
     */
    private function _dataPostExcelCheck(array $cells): bool
    {
        if ($cells[1]->getValue() !== '고유번호') {
            return false;
        }
        if ($cells[2]->getValue() !== '등급코드') {
            return false;
        }
        if ($cells[3]->getValue() !== '고객') {
            return false;
        }
        if ($cells[4]->getValue() !== '연락처') {
            return false;
        }
        if ($cells[5]->getValue() !== '상품금액') {
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
    private function _dataPostRead(array $cells): array
    {
        $rtn = [ 'result' => false, 'message' => '', 'data' => [] ];

        $temp = [
            'pm_number' => $cells[1]->getValue(), // 고유번호
            'pm_code' => $cells[2]->getValue(), // 등급코드
            'cus_name' => $cells[3]->getValue(), // 고객
            'cus_mobile' => $cells[4]->getValue(), // 연락처
            'bnft_price' => $cells[5]->getValue(), // 상품금액
        ];

        // 데이터 검증
        if (!$this->validation->check($temp['pm_number'], 'required')) {
            $rtn['message'] = '고유번호 값이 잘못 되었습니다.';
            return $rtn;
        }
        if (!$this->validation->check($temp['pm_code'], 'required|in_list[P1,P2]')) {
            $rtn['message'] = '등급코드 값이 잘못 되었습니다.';
            return $rtn;
        }
        if (!$this->validation->check($temp['cus_name'], 'required')) {
            $rtn['message'] = '고객 값이 잘못 되었습니다.';
            return $rtn;
        }
        if (!$this->validation->check($temp['cus_mobile'], 'required')) {
            $rtn['message'] = '연락처 값이 잘못 되었습니다.';
            return $rtn;
        }
        if (!$this->validation->check($temp['bnft_price'], 'required|is_natural|in_list[20000,25000,30000,35000,45000,60000,50000,75000,70000,95000]')) {
            $rtn['message'] = '상품금액 값이 잘못 되었습니다.';
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
    private function _dataPostReg(array $rows): array
    {
        $rtn = [ 'result' => true, 'message' => '', 'affected_row' => 0, 'dupli_row' => 0 ];

        $res = $this->prom_model->insertDataBulk($rows);
        $rtn['result'] = $res['result'];
        $rtn['message'] = $res['message'];
        $rtn['affected_row'] = $res['affected_row'];
        $rtn['dupli_row'] = count($rows) - $res['affected_row'];

        return $rtn;
    }
    /**
     * Create promotion customer
     *
     * @return array
     */
    private function _dataPostCustomer(): array
    {
        $rtn = [ 'result' => true, 'message' => '' ];

        // Create promotion user data
        $res_create = $this->prom_model->createCustomer();

        return $rtn;
    }


    /**
     * Delete promotion data
     *
     * @return mixed
     */
    private function _dataDelete()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'DELETE' ]);

        // Read parameters
        $pm_ids = $this->commonLib->readRawInput('ids', []);

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
        $res = $this->prom_model->deleteData($pm_ids);
        if (!$res['result']) {
            $rtn['result'] = false;
            $rtn['message'] = $res['message'];

            return $this->respond($rtn, 200, '');
        }

        $rtn['data']['affected_row'] = $res['affected_row'];

        return $this->respond($rtn, 200, '');
    }

}