<?php namespace App\Controllers\Admin;

/**
 * Kcar manage page controller
 */
class Kcar extends BaseController
{
    /**
     * /Admin/Kcar default page
     *
     * @return void
     */
    public function index()
    {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('');
    }


    /**
     * KW promotion list page
     *
     * @return void
     */
    public function kw_list()
    {
        $view_var = [
            'layout' => [
                'manager_name' => $this->session->get('admin')['manager_name']
            ]
        ];
        return view('admin/kcar/kw_list', $view_var);
    }
}