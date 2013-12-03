<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Rdio API Keys
|--------------------------------------------------------------------------
|
| Your API keys given to you by Rdio, if you do not have any you can
| sign up for a oauth1 account at:
|
|	http://developer.rdio.com/
|
*/
$config['rdio_token']	= 'd3ddstqmc77epb9q93m7j59c';
$config['rdio_secret']	= 'hAxpqwbWbq';

/*
|--------------------------------------------------------------------------
| System Config
|--------------------------------------------------------------------------
|
| Other useful settings
| 
|	domain: your domain name WITHOUT http://
|	allow_registration: allows new music dj's to register for site use
|	max_sites: maximum number of sites active at anytime by a user
|
*/
$config['domain']			 	= 'wizuma.com';
$config['allow_registration'] 	= true;
$config['max_sites']			= 1;

/*
|--------------------------------------------------------------------------
| JS Console
|--------------------------------------------------------------------------
|
| Enables verbose javascript for debugging purposes
|
*/
$config['debug_voters']	= true;
$config['debug_dj']		= true;