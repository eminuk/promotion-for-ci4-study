<?php namespace App\Entities\Admin;

use CodeIgniter\Entity;

class Kw extends Entity
{
    /**
     * commonLib Library
     *
     * @var [type]
     */
    private $commonLib;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        // Load libraries
        $this->commonLib = new \App\Libraries\CommonLib();
    }
}