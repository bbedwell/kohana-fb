<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_FB extends Controller {
	
	protected $me;
	
	protected $_fb_wrapper = 'Facebook';

	public function before()
	{
		
		$this->me = FB::instance()
						->auth($this->_fb_wrapper)
						->me();

		parent::before();
	}

} // End FB