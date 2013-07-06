<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voter_json extends CI_Controller {

	/**
	 * Backend Controller
	 * Runs Cron Functions
	 * 
	 */
	public function update_rdio()
	{
		//recalculate all queues
		//rebuild all rdio accounts
		//eventually 
	}

	public function purge_cache()
	{
		//once a day roughly 4am purge all completed votes
		//purge only events from 3 days after
	}
}