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

        $this->view_data['lalal'] = [
            'rarar'
        ];

        return view('admin/kcar/kw_list', $this->view_data);
    }
}