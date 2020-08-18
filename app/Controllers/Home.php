<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		// return view('welcome_message');

		// Go to specific UI
		return redirect()->to('/front/prom');
	}

	//--------------------------------------------------------------------

}
