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
$config['rdio_token']	= 'eaflme';
$config['rdio_secret']	= '';

/*
|--------------------------------------------------------------------------
| System Config
|--------------------------------------------------------------------------
|
| Other useful settings
| 
|	allow_registration: allows new music dj's to register for site use
|	allow_qr_code_login: if enabled allows deivces to login via QR codes
|
*/
$config['allow_registration'] 	= true;
$config['allow_qr_code_login']	= true;

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