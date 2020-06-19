<?php namespace App\Controllers\admin;

use CodeIgniter\Controller;

/**
 * Admin 기본 기능과 관련된 페이지 구현 컨트롤러
 */
class Home extends Controller
{
    public function index()
    {
        // throw new \CodeIgniter\Exceptions\PageNotFoundException('wwww');
        // $ee = 'autocarz!@34';
        // echo hash('SHA256', "salt{$ee}autocarz");

        $manager_model = new \App\Models\Admin\ManagerModel();

        $ee = null;
        // $res = $manager_model->find(1);
        // $res = $manager_model->findColumn('email');
        // $res = $manager_model->where('id', 2)->findColumn('email');
        // $res = $manager_model->findAll();
        // $res = $manager_model->where('id', 1)->findAll();
        // $res = $manager_model->findAll(1, 0);
        // $res = $manager_model->where('id', 1)->first();
        // var_dump($res);

        $manager_model->where('id', 2)->chunk(10, function ($data)
        {
            var_dump($data);
        });

        echo view('admin/login_page');
    }
}