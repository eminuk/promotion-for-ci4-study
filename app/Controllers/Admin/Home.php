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
        echo view('admin/login_page');
    }
}