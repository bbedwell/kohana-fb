<?php defined('SYSPATH') or die('No direct script access.');

if( !class_exists('Facebook') )
{
	require_once Kohana::find_file('vendor','facebook/src/facebook');
}

/**
 * Authorizes a user for Facebook Application
 * https://github.com/facebook/php-sdk
 *
 * @package    Kohana_FB
 * @author     Bryce Bedwell <bryce@familylink.com>
 * @copyright  FamilyLink.com
 *
 * Copyright (c) 2011 FamilyLink.com
 * 
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 **/
abstract class Kohana_FB {
	
	/**
	 * @var  Kohana_FB  Singleton static instance
	 */
	private static $_instance;
	
	/**
	 * @var  Facebook  Instance
	 */
	private $_facebook;
	
	/**
	 * @var  array  User information
	 */
	private $_me;
	
	/**
	 * @var  array  What fields to query about the user
	 */
	private $_fields = array();
	
	/**
	 * @var  boolean  Whether user has been authed
	 */
	private $_authed = FALSE;
	
	/**
	 * @var  string  The class name to use for the facebook sdk. Can be used with a wrapper
	 */
	private $_class;
	
	/**
	 * Authorizes the user
	 *
	 * @return  self
	 */
	public function auth($class = 'Facebook')
	{
		if( !$this->_class )
		{
			$this->_class = $class;
		}
		
		if( !$this->_authed && $this->_class )
		{
			// Instantiate new Facebook object
			$this->_facebook = new $this->_class(
				array(
					'appId'  => Kohana::$config->load('fb')->app_id,
					'secret' => Kohana::$config->load('fb')->app_secret,
				)
			);
		
			// Attempt to query information about the user
			try 
			{
				$this->_me = $this->_facebook
					->query( "/".$this->_facebook->getUser()."?fields=" . implode(',', $this->_fields) )
					->set_table('user')
					->set_lifetime(3600)
					->execute(TRUE);
			}

			// If failed attempt, redirect to login page (Auth)
			catch(Exception $error) 
			{
				if(strpos($error, 'OAuth') !== FALSE || $this->_facebook->getUser() == 0) 
				{
					if(Kohana::$config->load('fb')->canvas !== FALSE)
					{
						echo "
							<script>
								top.location.href = '{$this->_facebook->getLoginUrl()}';
							</script>";
					} 
					else 
					{
						header("Location: {$this->_facebook->getLoginUrl()}");
					}
					exit;
				}

				error_log($error);
			}
			
			$this->_authed = TRUE;
		}
		
		return $this;
	}

	/**
	 * Returns information about current user
	 *
	 * @returns  array  User information
	*/
	public function me()
	{
		if($this->_authed)
		{
			return $this->_me;
		}
	}
	
	/**
	 * Returns instance of Kohana_FB
	 *
	 * @returns  object  Kohana_FB
	*/
	public static function instance()
	{
		if(!isset(self::$_instance))
		{
			self::$_instance = new FB();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Returns instance of provided Facebook
	 *
	 * @returns  object  Facebook
	*/
	public static function get_facebook()
	{
		$instance = self::$_instance;
		
		if($instance->_authed)
		{
			return $instance->_facebook;
		}
	}

}