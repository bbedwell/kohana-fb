Kohana FB Class
===

This module is an add-on for Kohana v3.2.0 that makes facebook authentication easy.

Uses most current Facebook SDK v3.1.1.

Edit 'config/fb.php'

Usage
---

### Basic

Call this anywhere to authorize / get information about the user

	// To authenticate user
	FB::instance()->auth();
	
	// To return user data
	FB::instance()->me();
	
	// To access Facebook API
	FB::get_facebook()->api(/* API CALL */);
	
	// If a wrapper facebook class is being used
	FB::instance()->auth('MyFacebook');

### Controller

Extend Controller_FB_ to authorize all users

	class Controller_Welcome extends Controller_FB

Get user data in extended controller

	$user = $this->me;
	