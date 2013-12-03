<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dj extends CI_Controller {

	/**
	 * Primary Controller
	 * Controls DJ Functionality
	 * 
	 */

	function __construct()
  	{
    	parent::__construct();
    	$this->load->library('session');
    	$this->load->model('rdio');
    	$this->load->helper('url');
  	}

	public function index()
	{
		if ($this->session->userdata('id') != FALSE)
		{
			$this->load->view('dj');
		}
		else
		{
			$this->output->set_header("Location: " . site_url());
		}
	}

	public function connect_rdio()
	{
		$this->output->set_header("Location: " . $this->rdio->begin_authentication(site_url() . "/dj/auth_complete"));
	}

	public function auth_complete()
	{
    	$this->rdio->complete_authentication($this->input->get('oauth_verifier'));
    	$this->output->set_header("Location: " . site_url() . "/dj");
	}

  	public function live()
  	{
    	$this->config->load('party_vote');
    	$result = $this->rdio->call('getPlaybackToken', array('domain' => $this->config->item('domain')));
    	$data = array('playback' => TRUE, 'token' => $result->result);
    	$this->load->view('live', $data);
  	}
}

/* End of file dj.php */
/* Location: /application/controllers/dj.php */