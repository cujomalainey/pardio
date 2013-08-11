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
		$site_id = $this->session->userdata('site_id');
		while ($i < 150)
		{
			sleep(2);
			if ($this->common->queue_changed($site_id, $hash))
			{
				$this->send(array("queue" => $this->common->get_queue($site_id), "hash" => $this->common->get_queue_hash($site_id), "voters" => $this->common->get_voters($site_id)));
				break;
			}
			$i++;
		}		
	}

	public function remove($key)
	{
		$this->load->model('common');
		$this->dj->remove($key, $this->session->userdata('site_id'));
		$this->common->update_queue_hash($this->session->userdata('site_id'));
		$this->send('true');
	}

	public function played($key)
	{
		$this->load->model('common');
		$this->dj->played($key, $this->session->userdata('site_id'));
		$this->common->update_queue_hash($this->session->userdata('site_id'));
		$this->send(true);
	}

	public function mark_queued($tracks)
	{
		//to be changed to can play
		$this->dj->mark_queued(explode("_", $tracks), $this->session->userdata('site_id'));
	}

	private function send($data)
	{
		$this->output->set_output(json_encode($data));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */