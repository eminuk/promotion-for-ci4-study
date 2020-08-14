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
    private $prom_model;

    /**
     * Construct
     */
    public function __construct() {
        // Set base controller config
        $this->base_controller_cfg['auto_login_check'] = true;

        // Load models
        $this->prom_model = new \App\Models\PromModel();
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
    public function list()
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
    public function listExcel()
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

        // Get promotion list
        $res = $this->prom_model->getPromList($params, false);
        if (!$res['result']) {
            $this->commonLib->jsAlertBack($res['message']);
        }

        // Promotion data export excel - box/spout
        $this->_listExcelBoxSpout($file_name, $res['list']);

        // Promotion data export excel - PhpOffice\PhpSpreadsheet
        // $this->_listExcelPhpSpreadsheet($file_name, $res['list']);
    }

    /**
     * Promotion data export excel - box/spout
     *
     * @return void
     */
    private function _listExcelBoxSpout(string $file_name, array $rows): void
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
            '고객', '연락처', '상품금액', 
            '상품코드', '신청여부', '신청일자', 
            '신청 우편번호', '신청 주소', '신청 상세주소', '신청상품', 
        ];
        $row_from_values = WriterEntityFactory::createRowFromArray($header, $style_header);
        $writer->addRow($row_from_values);

        // Add list data
        foreach ($rows as $item) {
            $row = [
                $item['id'], $item['pm_number'], $item['pm_code'], 
                $item['cus_name'], $item['cus_mobile'], $item['bnft_price'], 
                $item['bnft_code'], $item['is_select_kr'], $item['select_at'], 
                $item['customer_zip'], $item['customer_addr1'], $item['customer_addr2'], $item['type_kr'], 
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
    private function _listExcelPhpSpreadsheet(string $file_name, array $rows): void
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
            $worksheet->getCell("D{$r}")->setValue($item['cus_name']);
            $worksheet->getCell("E{$r}")->setValue($item['cus_mobile']);
            $worksheet->getCell("F{$r}")->setValue($item['bnft_price']);
            $worksheet->getCell("G{$r}")->setValue($item['bnft_code']);
            $worksheet->getCell("H{$r}")->setValue($item['is_select_kr']);
            $worksheet->getCell("I{$r}")->setValue($item['select_at']);
            $worksheet->getCell("J{$r}")->setValue($item['customer_zip']);
            $worksheet->getCell("K{$r}")->setValue($item['customer_addr1']);
            $worksheet->getCell("L{$r}")->setValue($item['customer_addr2']);
            $worksheet->getCell("M{$r}")->setValue($item['type_kr']);

            // PhpSpreadsheet - Add background color for even rows
            if (($r % 2) == 1) {
                $worksheet->getStyle("A{$r}:M{$r}")
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
        $worksheet->getStyle("A2:M{$r}")->applyFromArray($style_border);

        // PhpSpreadsheet - Set text wrap
        $worksheet->getStyle("A2:M{$r}")->getAlignment()->setWrapText(true);

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