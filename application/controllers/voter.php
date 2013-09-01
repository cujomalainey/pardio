<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voter extends CI_Controller {

	/**
	 * Primary Controller
	 * Controls Voter Functionality
	 * 
	 */
	
	function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }


	public function index()
	{
		if ($this->session->userdata('site_id') != false)
		{
			$this->output->set_header("Location: http://wizuma.com/index.php/voter/party");
		}
		else
		{
			$this->load->view('home');
		}
	}

	public function party()
	{
		if ($this->session->userdata('site_id') != false)
		{
			$this->load->view('voter');
		}
		else
		{
			$this->output->set_header("Location: http://wizuma.com");
		}
	}

	public function qr_code($site_id)
	{
		echo urldecode($site_id);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */