<?php namespace App\Controllers\Kcar;

/**
 * Kcar KW Promotion front page controller
 */
class Kw extends \App\Controllers\Kcar\BaseController
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
        // Load models
        $this->prom_model = new \App\Models\PromModel();
    }


    /**
     * /Kcar/Kw default page
     *
     * @return void
     */
    public function index()
    {
        return view('Kcar/kw_home', $this->view_data);
    }


    public function customer()
    {
        // Read parameters
        $params = [
            'cus_name' => $this->commonLib->readPostGet('cus_name'),
            'cus_mobile' => $this->commonLib->readPostGet('cus_mobile'),
        ];

        // Validate parameter
        $validate_alert = '이름, 휴대폰번호를 다시 입력해주세요.\n(문의 02-555-0206 오토카지 고객센터)';
        if (empty($params['cus_name'])) {
            // return redirect()->to('/Kcar/Kw');
            $this->commonLib->jsAlertRedirect($validate_alert, '/Kcar/Kw');
        }
        if (!$this->commonLib->isMobileNum($params['cus_mobile'])) {
            // return redirect()->to('/Kcar/Kw');
            $this->commonLib->jsAlertRedirect($validate_alert, '/Kcar/Kw');
        }

        // Get customer info
        $res = $this->prom_model->getCustomerInfo($params['cus_name'], $params['cus_mobile']);
        if (!$res['result']) {
            // return redirect()->to('/Kcar/Kw');
            $this->commonLib->jsAlertRedirect('페이지에 오류가 발생했습니다. (1)', '/Kcar/Kw');
        }

        if (empty($res['row']['type'])) {
            // 상품 선택화면

            // Get selectable product list
            $res_product = $this->prom_model->getKwProductInfo($res['row']['pm_id']);
            if (!$res_product['result']) {
                // return redirect()->to('/Kcar/Kw');
                $this->commonLib->jsAlertRedirect('페이지에 오류가 발생했습니다. (2)', '/Kcar/Kw');
            }

            // Set view data
            $this->view_data['view'] = [
                'cus_name' => $params['cus_name'],
                'cus_mobile' => $params['cus_mobile'],
                // 'cus_zip' => '',
                // 'cus_addr1' => '',
                // 'cus_addr2' => '',
            ];

            foreach ($res_product['list'] as $item) {
                switch ($item['type']) {
                    case '1':
                        $this->view_data['view']['wash_service'] = $item;
                        break;
                    case '2':
                        $this->view_data['view']['wash_goods'] = $item;
                        break;
                    case '3':
                        $this->view_data['view']['car_goods'] = $item;
                        break;
                    default:
                        break;
                }
            // $this->view_data['view']['cus_zip'] = $item['cus_zip'];
            // $this->view_data['view']['cus_addr1'] = $item['cus_addr1'];
            // $this->view_data['view']['cus_addr2'] = $item['cus_addr2'];
            }

            return view('Kcar/kw_cus_product', $this->view_data);
        } else {
            // 선택한 상품 정보

            // Set view data
            $this->view_data['view'] = [
                'cus_name' => $params['cus_name'],
                'cus_mobile' => $params['cus_mobile'],
                'data' => $res['row']
            ];

            return view('Kcar/kw_cus_apply', $this->view_data);
        }
    }
}