<?php namespace App\Controllers\API\Admin;


/**
 * Kcar 관련 API 컨트롤러
 */
class Kcar extends \App\Controllers\API\BaseController
{
    /**
     * Construct
     */
    public function __construct() {}

    public function kw_list()
    {
        // Check login info

        // Validate allowed method
        $this->validateAllowedMethod([ 'GET' ]);

        // Validate parameter
        $this->validateParameter([
            'email' => '',
            'password' => ''
        ]);

        // Read parameters
        $params = [
            'email' => $this->request->getPostGet('email'),
            'password' => $this->request->getPostGet('password'),
        ];

        // Set default response data
        $rtn = [
            'result' => false,
            'message' => 'Login fail.'
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
                    'redirect_url' => '/admin/kcar/kw_list'
                ]
            ];
        }

        return $this->respond($rtn, 200, '');
    }
}