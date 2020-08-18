<?php namespace App\Controllers\Front;

/**
 * Promotion front page controller
 */
class Prom extends \App\Controllers\Front\BaseController
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
     * /Front/Prom default page
     *
     * @return void
     */
    public function index()
    {
        return view('Front/prom_home', $this->view_data);
    }


    /**
     * /Front/Prom/customer page
     *
     * @return void
     */
    public function customer()
    {
        // Read parameters
        $params = [
            'cus_name' => $this->commonLib->readPostGet('cus_name'),
            'cus_mobile' => $this->commonLib->readPostGet('cus_mobile'),
        ];

        // Validate parameter
        $validate_alert = 'No matching information.';
        if (empty($params['cus_name'])) {
            $this->commonLib->jsAlertRedirect($validate_alert, '/front/prom');
        }
        if (!$this->commonLib->isMobileNum($params['cus_mobile'])) {
            $this->commonLib->jsAlertRedirect($validate_alert, '/front/prom');
        }

        // Get customer info
        $res = $this->prom_model->getCustomerInfo($params['cus_name'], $params['cus_mobile']);
        if (!$res['result']) {
            $this->commonLib->jsAlertRedirect('An error has occurred. (1)', '/front/prom');
        }

        if (empty($res['row']['type'])) {
            // 상품 선택화면

            // Get selectable product list
            $res_product = $this->prom_model->getPromProductInfo($res['row']['pm_id']);
            if (!$res_product['result']) {
                $this->commonLib->jsAlertRedirect('An error has occurred. (2)', '/front/prom');
            }

            // Set view data
            $this->view_data['view'] = [
                'cus_name' => $params['cus_name'],
                'cus_mobile' => $params['cus_mobile'],
            ];

            foreach ($res_product['list'] as $item) {
                switch ($item['type']) {
                    case '1':
                        $this->view_data['view']['option_1'] = $item;
                        break;
                    case '2':
                        $this->view_data['view']['option_2'] = $item;
                        break;
                    case '3':
                        $this->view_data['view']['option_3'] = $item;
                        break;
                    default:
                        break;
                }
            }

            return view('Front/prom_cus_product', $this->view_data);
        } else {
            // 선택한 상품 정보

            // Set view data
            $this->view_data['view'] = [
                'cus_name' => $params['cus_name'],
                'cus_mobile' => $params['cus_mobile'],
                'data' => $res['row']
            ];

            return view('Front/prom_cus_apply', $this->view_data);
        }
    }
}