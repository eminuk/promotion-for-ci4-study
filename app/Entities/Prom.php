<?php namespace App\Entities;

use CodeIgniter\Entity;

class Prom extends Entity
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