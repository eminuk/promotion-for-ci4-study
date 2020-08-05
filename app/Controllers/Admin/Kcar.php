<?php namespace App\Controllers\Admin;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Row;


/**
 * Kcar manage page controller
 */
class Kcar extends \App\Controllers\Admin\BaseController
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
    public function __construct() {
        // Set base controller config
        $this->base_controller_cfg['auto_login_check'] = true;

        // Load models
        $this->_Kw_model = new \App\Models\Admin\KwModel();
    }

    /**
     * /Admin/Kcar default page
     *
     * @return void
     */
    public function index()
    {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('');
    }


    /**
     * KW promotion list page
     *
     * @return void
     */
    public function kwList()
    {
        // Set view date
        // $this->view_data['sample'] = [ 'first' => 1 ];

        return view('admin/kcar/kw_list', $this->view_data);
    }

    /**
     * KW promotion data export excel
     *
     * @return void
     */
    public function kwListExcel()
    {
        // Set file name
        $file_name = 'KW_List__'.date('YmdHis').'.xlsx';

        // box/spout - Create default style
        $style_default = (new StyleBuilder())
            ->setShouldWrapText()
            ->build();

        // box/spout - Create writer and setting
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->setDefaultRowStyle($style_default)
            ->openToBrowser($file_name);

        // box/spout - Create border and style for header
        $border_header = (new BorderBuilder())
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->build();
        $style_header = (new StyleBuilder())
            ->setBorder($border_header)
            ->setCellAlignment(CellAlignment::CENTER)
            ->build();

        // box/spout - Set columns header and add
        $header = [
            '순번', '상품', '모델', '차량번호', '고객', '연락처', '상품권금액', 
            '상품코드', '신청여부', '신청일자', '우편번호', '주소', '상세주소', '신청상품', '발송상태'
        ];
        $row_from_values = WriterEntityFactory::createRowFromArray($header, $style_header);
        $writer->addRow($row_from_values);


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

        $error = false;
        $error_massage = '';

        // Validate parameter
        if (!$error && !$this->validation->check($params['is_select'], 'required|in_list[ALL,Y,N]')) {
            $error = false;
            $error_massage = 'is_select: '.$this->validation->getError();
        }
        if (!$error && !$this->validation->check($params['search_key'], 'required_with[search_value]')) {
            $error = false;
            $error_massage = 'search_key: '.$this->validation->getError();
        }
        if (!$error && !$this->validation->check($params['sdate'], 'required|valid_date')) {
            $error = false;
            $error_massage = 'sdate: '.$this->validation->getError();
        }
        if (!$error && !$this->validation->check($params['edate'], 'required|valid_date')) {
            $error = false;
            $error_massage = 'edate: '.$this->validation->getError();
        }
        // Additional validate parameter
        if (!$error && !empty($params['search_value'])) {
            if (!$this->validation->check($params['search_key'], 'in_list[kw_code,car_number,car_model,cus_name,cus_mobile,cus_zip,cus_addr1,cus_addr2,bnft_price,bnft_code,product]')) {
                $error = false;
                $error_massage = 'search_key: '.$this->validation->getError();
            }
        }
        
        $res = [];
        if (!$error) {
            // Get Kw list
            $res = $this->_Kw_model->getKwList($params, false);
            if (!$res['result']) {
                $error = false;
                $error_massage = $res['message'];
            }
        }

        // Response error message
        if ($error) {
            $row = [ $error_massage ];
            $style_reset = (new StyleBuilder())
                ->setShouldWrapText(false)
                ->build();
            $row_from_values = WriterEntityFactory::createRowFromArray($row, $style_reset);
            $writer->addRow($row_from_values);

            // Response
            $writer->close();
            exit();
        }

        // Add list data
        foreach ($res['list'] as $item) {
            $row = [
                $item['id'], $item['kw_code'], $item['car_model'], $item['car_number'], 
                $item['cus_name'], $item['cus_mobile'], $item['bnft_price'], 
                $item['bnft_code'], $item['is_select_kr'], $item['select_at'], 
                $item['cus_zip'], $item['cus_addr1'], $item['cus_addr2'], $item['type_kr'],
                $item['send_sms_kr']
            ];

            // box/spout - Set row and add
            $row_from_values = WriterEntityFactory::createRowFromArray($row);
            $writer->addRow($row_from_values);

            unset($row, $row_from_values);
        }

        // box/spout - Response
        $writer->close();
        exit();
    }


    /**
     * KW promotion data import excel sample
     *
     * @return void
     */
    public function kwExcelSample()
    {
        // Set file name
        $file_name = FCPATH.'asset/sample/sample__20200703000000.xlsx';
        $download_name = 'sample.xlsx';
        
        return $this->response->download($file_name, null)
            ->setFileName($download_name);

    }
}