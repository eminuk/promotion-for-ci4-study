<?php namespace App\Controllers\Admin;

use CodeIgniter\CodeIgniter;

/**
 * Admin default page controller
 */
class Home extends \CodeIgniter\Controller
{
    /**
     * session variable
     *
     * @var [session]
     */
    public $session;

    /**
     * Construct
     */
    public function __construct()
    {
        // Load libraries 
        $this->session = \Config\Services::session();
    }

    /**
     * /Admin default page - Login
     *
     * @return void
     */
    public function index()
    {
        // Log out
        // $this->session->destroy();
        // $this->session->stop();
        $this->session->remove('admin_login');

        return view('admin/login_page');
    }
}