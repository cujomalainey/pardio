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

	public function get_queue($hash)
	{
		$this->load->model('common');
		$i = 0;
		while ($i < 150)
		{
			sleep(2);
			//recalculate votes
			if ($this->common->get_queue_hash($this->session->userdata('site_id')) != $hash)
			{
				if($this->common->queue_changed($hash)) {
					$this->send($this->common->get_queue($this->session->userdata('site_id')));
				} else {
					$this->send(array("queue" => array()));
				}
			}
			$i++;
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