<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dj extends CI_Controller {

	/**
	 * Seconday Controller
	 * Controls DJ Functionality
	 * 
	 */

	function __construct()
  	{
    	parent::__construct();
    	$this->load->library('session');
    	$this->load->model('rdio');
  	}

	public function index()
	{
		if ($this->session->userdata('id') != FALSE)
		{
			$this->load->view('dj');
		}
		else
		{
			$this->output->set_header("Location: http://wizuma.com/");
		}
	}

	public function connect_rdio()
	{
		$this->output->set_header("Location: " . $this->rdio->begin_authentication("http://wizuma.com/index.php/dj/auth_complete"));
	}

	public function auth_complete()
	{
    $this->rdio->complete_authentication($this->input->get('oauth_verifier'));
	}

  	public function test()
  	{
    	$result = $this->rdio->call('getPlaybackToken', array('domain' => 'wizuma.com'));
    	$data = array('playback' => TRUE, 'token' => $result->result);
    	$this->load->view('test', $data);
  	}
}

/* End of file dj.php */
/* Location: ./application/controllers/dj.php */