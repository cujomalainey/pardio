<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dj_json extends CI_Controller {

	/**
	 * Secondary Controller
	 * Controls DJ Functionality
	 * 
	 */

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		//if ($this->session->userdata('user_id') == FALSE)
		{
		//	header("Location: http://wizuma.com");
		//	exit();
		}
		$this->output->set_content_type('application/json');
        $this->load->model("dj");
	}

	public function get_queue()
	{
		$this->load->model('common');
		if(isset($_GET['hash'])) {
			if($this->common->queue_changed($_GET['hash'])) {
				$this->send($this->common->get_queue($this->session->userdata('site_id')));
				return;
			} else {
				$this->send(array("queue" => array()));
				return;
			}
		}

		$this->send($this->common->get_queue($this->session->userdata('site_id')));		
	}

	public function mark_queued($tracks)
	{
		$this->dj->mark_queued(explode("_", $tracks), $this->session->userdata('site_id'));
	}

	private function send($data)
	{
		$this->output->set_output(json_encode($data));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */