<?php namespace App\Controllers\Admin;

/**
 * Admin default page controller
 */
class Home extends \App\Controllers\Admin\BaseController
{
    /**
     * Construct
     */
    public function __construct()
    {
        // Set base controller config
        $this->base_controller_cfg['auto_login_check'] = false;
    }

    /**
     * /Admin default page - Login
     *
     * @return void
     */
    public function index()
    {
        // Log out
        $this->session->remove('admin_login');
        // $this->session->stop();
        // $this->session->destroy();

        return view('admin/login_page');
    }
}