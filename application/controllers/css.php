<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Css extends CI_Controller {

	/**
	 * CSS Controller
	 * Controls CSS Colors
	 * 
	 */
	
	function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->output->set_header("content-type: text/css");
    }

	public function jsmobile_voter()
	{
		if ($this->session->userdata('site_id') != false)
		{
			$this->load->view('voter');
		}
		else
		{
			$this->output->set_header("Location: " . site_url());
		}
	}
}

/* End of file css.php */
/* Location: ./application/controllers/css.php */