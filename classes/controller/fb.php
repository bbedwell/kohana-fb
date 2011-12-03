<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_FB extends Controller {
	
	protected $me;

	public function before()
	{
		$this->me = FB::instance()->me();
		
		parent::before();
	}

} // End FB
