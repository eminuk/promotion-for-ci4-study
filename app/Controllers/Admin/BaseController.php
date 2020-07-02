<?php namespace App\Controllers\Admin;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;

/**
 * Base controller class for controllers under 'App\Controllers\Admin'
 */
class BaseController extends Controller
{
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * session variable
     *
     * @var session
     */
    protected $session;

    /**
     * validation variable
     *
     * @var [type]
     */
    protected $validation;

    /**
     * CommonLib
     *
     * @var CommonLib
     */
    protected $commonLib;

    /**
     * Predefined variable for layout and view
     * (reserved word: admin_layout)
     *
     * @var array
     */
    protected $view_data = [
        'admin_layout' => []
    ];

    /**
     * Base controller config variable
     *
     * @var array
     */
    protected $base_controller_cfg = [
        'auto_login_check' => true
    ];


    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();

        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->commonLib = new \App\Libraries\CommonLib();


        // Check login session and redirect login page
        $this->isLogin(true);
    }

    /**
     * Check login session and redirect login page
     *
     * @param boolean $is_auto
     * @return void
     */
    protected function isLogin(bool $is_auto = false): void
    {
        // Check config
        if ($is_auto && !$this->base_controller_cfg['auto_login_check']) {
            return;
        }

        // Check session and make view data for admin layout.
        if (!$this->session->has('admin_login')) {
            redirect()->to('/admin')->send();
            exit();
        } else {
            $this->view_data = [
                'admin_layout' => [
                    'manager_name' => $this->session->get('admin_login')['manager_name']
                ]
            ];
            return;
        }
    }
}
