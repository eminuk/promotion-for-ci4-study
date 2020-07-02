<?php namespace App\Controllers\API;

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
 * Base controller class for controllers under 'App\Controllers\API'
 */
class BaseController extends Controller
{
    use \CodeIgniter\API\ResponseTrait;

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
     * CommonLib instance
     *
     * @var CommonLib
     */
    protected $commonLib;

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

        // Set ResponseTrait
        $this->setResponseFormat('json');

        // Check login session and redirect login page
        $this->validateLogin(true);
    }


    /**
     * Validate allowed method
     *
     * @param array $allowed_method
     * @return void
     */
    protected function validateAllowedMethod(array $allowed_method = []): void
    {
        if (!in_array($this->request->getMethod(TRUE), $allowed_method)) {
            $this->fail($this->request->getMethod(TRUE).' is not allowed', 405, 'Method Not Allowed', '');
            $this->response->send();
            exit();
        }
    }

    /**
     * Validte parameter
     *
     * @param array $validate_rule
     * @return void
     */
    protected function validateParameter(array $validate_rule = []): void
    {
        if (!$this->validate($validate_rule)) {
            $this->fail(
                $this->validator->getErrors(),
                422,
                'Parameter validate is fail',
                'Parameter validate is fail'
            );
            $this->response->send();
            exit();
        }
    }

    /**
     * Validte variable
     *
     * @param [type] $var
     * @param string $validate_rule
     * @return void
     */
    protected function validateVariable($var, string $validate_rule): void
    {
        if (!$this->validation->check($var, $validate_rule)) {
            $this->fail(
                $this->validation->getErrors(),
                422,
                'Parameter validate is fail',
                'Parameter validate is fail'
            );
            $this->response->send();
            exit();
        }
    }

    /**
     * Check login session and response.
     *
     * @param boolean $is_auto
     * @return void
     */
    protected function validateLogin(bool $is_auto = false): void
    {
        // Check config
        if ($is_auto && !$this->base_controller_cfg['auto_login_check']) {
            return;
        }

        // Check session
        if (!$this->session->has('admin_login')) {
            $this->failUnauthorized('Administer login is required.', 'Unauthorized', '');
            $this->response->send();
            exit();
        }
    }
}
