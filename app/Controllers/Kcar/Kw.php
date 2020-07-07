<?php namespace App\Controllers\Kcar;

/**
 * Kcar KW Promotion front page controller
 */
class Kw extends \App\Controllers\Kcar\BaseController
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
        // Load models
        $this->_Kw_model = new \App\Models\Admin\KwModel();
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

        error_log(var_export($params, true));

        // Validate parameter
        if (empty($params['cus_name'])) {
            return redirect()->to('/Kcar/Kw');
        }
        if (!$this->commonLib->isMobileNum($params['cus_mobile'])) {
            return redirect()->to('/Kcar/Kw');
        }
        
        // Get customer info
        $res = $this->_Kw_model->getCustomerInfo($params['cus_name'], $params['cus_mobile']);
        if (!$res['result']) {
            return redirect()->to('/Kcar/Kw');
        }

        if (empty($res['row']['type'])) {
            // 상품 선택화면

            // Get selectable product list
            $res_product = $this->_Kw_model->getKwProductInfo($res['row']['kw_code'], $res['row']['bnft_price']);
            if (!$res_product['result']) {
                return redirect()->to('/Kcar/Kw');
            }

            // Set view data
            $this->view_data['view'] = [
                'cus_name' => $params['cus_name'],
                'cus_mobile' => $params['cus_mobile']
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