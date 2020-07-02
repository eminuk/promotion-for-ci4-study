<?php namespace App\Controllers\API\Admin;


/**
 * 사용자(관리자) 관련 API 컨트롤러
 */
class Manager extends \App\Controllers\API\BaseController
{
    /**
     * Manager model
     *
     * @var [type]
     */
    private $_manager_model;

    /**
     * Construct
     */
    public function __construct()
    {
        // Set base controller config
        $this->base_controller_cfg['auto_login_check'] = false;

        // Load models
        $this->_manager_model = new \App\Models\Admin\ManagerModel();
    }

    /**
     * Login API
     *
     * @return void
     */
    public function login()
    {
        // Remove login info
        $this->session->remove('admin_login');

        // Validate allowed method
        $this->validateAllowedMethod([ 'POST' ]);

        // Validate parameter
        $this->validateParameter([
            'email' => 'required|valid_email',
            'password' => 'required'
        ]);

        // Read parameters
        $params = [
            'email' => $this->commonLib->readPostGet('email'),
            'password' => $this->commonLib->readPostGet('password'),
        ];

        // Set default response data
        $rtn = [
            'result' => false,
            'message' => 'Login is fail',
            'data' => []
        ];

        // Get Manager entity from email
        $manager = $this->_manager_model->getFromEmail($params['email']);
        if (!isset($manager)) {
            return $this->respond($rtn, 200, '');
        }

        // Check manager's password and status
        $check_result = $manager->checkPassword($params['password']);

        // Set response data & create login info
        if ($check_result) {
            // Set response data
            $rtn = [
                'result' => true,
                'message' => '',
                'data' => [
                    'redirect_url' => '/admin/kcar/kwList'
                ]
            ];

            // Create login info
            $this->session->set('admin_login', [
                'manager_id' => $manager->id,
                'manager_email' => $manager->email,
                'manager_name' => $manager->name
            ]);
        }

        return $this->respond($rtn, 200, '');
    }

}