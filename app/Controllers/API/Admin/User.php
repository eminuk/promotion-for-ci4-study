<?php namespace App\Controllers\API\Admin;

use CodeIgniter\API\ResponseTrait;

/**
 * 사용자(관리자) 관련 API 컨트롤러
 */
class User extends BaseController
{
    private $_manager_model;

    public function __construct()
    {
        // Load models
        $this->_manager_model = new \App\Models\Admin\ManagerModel();
    }
    public function login()
    {
        // Validate allowed method
        $this->validateAllowedMethod([ 'GET', 'POST' ]);

        // Validate parameter
        $this->validateParameter([
            'email' => 'required|valid_email',
            'password' => 'required'
        ]);

        $ee = $this->request->getPostGet('email');
        var_dump($ee);

        return $this->respond([ 'status' => 200, 'b' ], 200, '');

        // throw new \CodeIgniter\Exceptions\PageNotFoundException('wwww');
        // $ee = 'autocarz1234';
        // echo hash('SHA256', "salt{$ee}autocarz");


        // var_dump($this->commonLib->hashPassword('autocarz1234'));

        $ee = null;
        // $res = $manager_model->find(1);
        // $res = $manager_model->findColumn('email');
        // $res = $manager_model->where('id', 2)->findColumn('email');
        // $res = $manager_model->findAll();
        // $res = $manager_model->where('id', 1)->findAll();
        // $res = $manager_model->findAll(1, 0);
        // $res = $manager_model->where('id', 1)->first();
        // var_dump($res);

        // $manager_model->where('id', 2)->chunk(10, function ($data)
        // {
        //     var_dump($data);
        // });

        $manager = $this->_manager_model->getFromEmail('admin@autocarz.co.kr');
        // var_dump($manager);
        $res = $manager->checkPassword('autocarz1234');
        var_dump($res);

        // echo view('admin/login_page');
    }
}