<?php namespace App\Controllers\Admin;

/**
 * Admin default page controller
 */
class Home extends BaseController
{
    /**
     * Admin default page - Login
     *
     * @return void
     */
    public function index()
    {
        // Log out
        // $this->session->destroy();
        // $this->session->stop();
        $this->session->remove('admin');

        return view('admin/login_page');
    }
}