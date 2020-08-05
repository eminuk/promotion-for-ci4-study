<?php namespace App\Controllers\Admin;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Row;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Promotion manage page controller
 */
class Prom extends \App\Controllers\Admin\BaseController
{
    /**
     * Prom model
     *
     * @var [type]
     */
    private $Prom_model;

    /**
     * Construct
     */
    public function __construct() {
        // Set base controller config
        $this->base_controller_cfg['auto_login_check'] = true;

        // Load models
        $this->Prom_model = new \App\Models\PromModel();
    }

    /**
     * /Admin/Prom default page
     *
     * @return void
     */
    public function index()
    {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('');
    }


    /**
     * Promotion list page
     *
     * @return void
     */
    public function List()
    {
        // Set view date
        // $this->view_data['sample'] = [ 'first' => 1 ];

        return view('Admin/Prom/list', $this->view_data);
    }

    /**
     * Promotion data export excel
     *
     * @return void
     */
    public function kwListExcel()
    {
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

        // Validate parameter
        if (!$this->validation->check($params['is_select'], 'required|in_list[ALL,Y,N]')) {
            $this->commonLib->jsAlertBack('is_select: '.$this->validation->getError());
        }
        if (!$this->validation->check($params['search_key'], 'required_with[search_value]')) {
            $this->commonLib->jsAlertBack('search_key: '.$this->validation->getError());
        }
        // Additional validate parameter
        if (!empty($params['sdate']) && !$this->validation->check($params['sdate'], 'valid_date')) {
            $this->commonLib->jsAlertBack('sdate: '.$this->validation->getError());
        }
        if (!empty($params['edate']) && !$this->validation->check($params['edate'], 'valid_date')) {
            $this->commonLib->jsAlertBack('edate: '.$this->validation->getError());
        }
        if (!empty($params['search_value'])) {
            if (!$this->validation->check($params['search_key'], 'in_list[pm_code,car_number,car_model,cus_name,cus_mobile,cus_zip,cus_addr1,cus_addr2,bnft_price,bnft_code,product]')) {
                $this->commonLib->jsAlertBack('search_key: '.$this->validation->getError());
            }
        }

        // Set file name
        $file_name = 'promotion_list__'.date('YmdHis').'.xlsx';

        // Get Kw list
        $res = $this->Prom_model->getKwList($params, false);
        if (!$res['result']) {
            $this->commonLib->jsAlertBack($res['message']);
        }

        // Promotion data export excel - box/spout
        // $this->_kwListExcelBoxSpout($file_name, $res['list']);
        // Promotion data export excel - PhpOffice\PhpSpreadsheet
        $this->_kwListExcelPhpSpreadsheet($file_name, $res['list']);
    }

    /**
     * Promotion data export excel - box/spout
     *
     * @return void
     */
    private function _kwListExcelBoxSpout(string $file_name, array $rows): void
    {
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
            '순번', '고유번호', '등급코드', 
            // '상품금액', '발급지점', '발급일자', '구분',
            // '차량번호', '제조사', '모델', '대분류', '소분류', '연식',
            '고객', '연락처', 
            // '우편번호', '주소', '상세주소', 
            '상품권금액', 
            // '상품권종류', '세차 서비스', 
            '상품코드', '신청여부', '신청일자', 
            '신청 우편번호', '신청 주소', '신청 상세주소', 
            // '주소 변경', 
            '신청상품', '발송상태'
        ];
        $row_from_values = WriterEntityFactory::createRowFromArray($header, $style_header);
        $writer->addRow($row_from_values);

        // Add list data
        foreach ($rows as $item) {
            $row = [
                $item['id'], $item['pm_number'], $item['pm_code'], 
                // $item['kw_price'], $item['kw_branch'], $item['kw_date'], $item['kw_type'], 
                // $item['car_number'], $item['car_manufacturer'], $item['car_model'], $item['car_trim'], $item['car_level'], $item['car_year'], 
                $item['cus_name'], $item['cus_mobile'], 
                // $item['cus_zip'], $item['cus_addr1'], $item['cus_addr2'], 
                $item['bnft_price'], 
                // $item['bnft_type'], (($item['wash_service'] == 1)? '': 'x'), 
                $item['bnft_code'], $item['is_select_kr'], $item['select_at'], 
                $item['customer_zip'], $item['customer_addr1'], $item['customer_addr2'], 
                // $item['addr_type_kr'],
                $item['type_kr'], $item['send_sms_kr']
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
     * Promotion data export excel - PhpOffice\PhpSpreadsheet
     *
     * @return void
     */
    private function _kwListExcelPhpSpreadsheet(string $file_name, array $rows): void
    {
        // PhpSpreadsheet - Load template
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(FCPATH.'asset/sample/template__20200729000000.xlsx');

        // PhpSpreadsheet - Get worksheet
        $worksheet = $spreadsheet->getActiveSheet();

        for ($i = 0, $len = count($rows); $i < $len; $i++) {
            $item = $rows[$i];
            $r = $i + 2;

            // PhpSpreadsheet - Set sell value
            $worksheet->getCell("A{$r}")->setValue($item['id']);
            $worksheet->getCell("B{$r}")->setValue($item['pm_number']);
            $worksheet->getCell("C{$r}")->setValue($item['pm_code']);
            // $worksheet->getCell("D{$r}")->setValue($item['kw_price']);
            // $worksheet->getCell("E{$r}")->setValue($item['kw_branch']);
            // $worksheet->getCell("F{$r}")->setValue($item['kw_date']);
            // $worksheet->getCell("G{$r}")->setValue($item['kw_type']);

            // $worksheet->getCell("H{$r}")->setValue($item['car_number']);
            // $worksheet->getCell("I{$r}")->setValue($item['car_manufacturer']);
            // $worksheet->getCell("J{$r}")->setValue($item['car_model']);
            // $worksheet->getCell("K{$r}")->setValue($item['car_trim']);
            // $worksheet->getCell("L{$r}")->setValue($item['car_level']);
            // $worksheet->getCell("M{$r}")->setValue(date('M.y', strtotime($item['car_year'])));

            $worksheet->getCell("N{$r}")->setValue($item['cus_name']);
            $worksheet->getCell("O{$r}")->setValue($item['cus_mobile']);
            // $worksheet->getCell("P{$r}")->setValue($item['cus_zip']);
            // $worksheet->getCell("Q{$r}")->setValue($item['cus_addr1']);
            // $worksheet->getCell("R{$r}")->setValue($item['cus_addr2']);

            $worksheet->getCell("S{$r}")->setValue($item['bnft_price']);
            // $worksheet->getCell("T{$r}")->setValue($item['bnft_type']);
            // $worksheet->getCell("U{$r}")->setValue((($item['wash_service'] == 1)? '': 'x'));

            $worksheet->getCell("V{$r}")->setValue($item['bnft_code']);
            $worksheet->getCell("W{$r}")->setValue($item['is_select_kr']);
            $worksheet->getCell("X{$r}")->setValue($item['select_at']);

            $worksheet->getCell("Y{$r}")->setValue($item['customer_zip']);
            $worksheet->getCell("Z{$r}")->setValue($item['customer_addr1']);
            $worksheet->getCell("AA{$r}")->setValue($item['customer_addr2']);
            // $worksheet->getCell("AB{$r}")->setValue($item['addr_type_kr']);

            $worksheet->getCell("AC{$r}")->setValue($item['type_kr']);
            $worksheet->getCell("AD{$r}")->setValue($item['send_sms_kr']);

            // PhpSpreadsheet - Add background color for even rows
            if (($r % 2) == 1) {
                $worksheet->getStyle("A{$r}:AD{$r}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('F9F9F9');
            }

            unset($item);
        }

        // PhpSpreadsheet - Add border outline and inside
        $style_border = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
                'inside' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $worksheet->getStyle("A2:AD{$r}")->applyFromArray($style_border);

        // PhpSpreadsheet - Set text wrap
        $worksheet->getStyle("A2:AD{$r}")->getAlignment()->setWrapText(true);

        // PhpSpreadsheet - Set active sell
        $worksheet->getStyle("A2");

        // Add header for file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');

        // PhpSpreadsheet - Response
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }


    /**
     * Promotion data import excel sample
     *
     * @return void
     */
    public function importExcelSample()
    {
        // Set file name
        $file_name = FCPATH.'asset/sample/import_sample__20200723000000.xlsx';
        $download_name = 'import_sample.xlsx';
        
        return $this->response->download($file_name, null)
            ->setFileName($download_name);

    }
}