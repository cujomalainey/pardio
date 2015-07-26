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
			if ($this->common->queue_changed($site_id, $hash))
			{
				$this->send(array("queue" => $this->dj->get_queue($site_id), "hash" => $this->common->get_queue_hash($site_id)));
				break;
			}
			sleep(2);
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

	public function mark_stream($track, $streamable)
	{
		//to be changed to can play
		$this->dj->mark_stream($track, $streamable, $this->session->userdata('site_id'));
		$this->load->model('common');
		$this->common->update_queue_hash($this->session->userdata('site_id'));
		$this->send($streamable);
	}

	public function check_messages($last_message_id)
	{
		$i = 0;
		$site_id = $this->session->userdata('site_id');
		while ($i < 150)
		{
			if ($this->dj->new_messages($site_id, $last_message_id))
			{
				$this->send(array("last_id" => $this->dj->last_message_id($site_id), "messages" => $this->dj->get_messages($side_id, $last_message), "voters" => $this->common->get_voters($site_id)));
				break;
			}
			sleep(2);
			$i++;
		}	
	}

	private function send($data)
	{
		$this->output->set_output(json_encode($data));
	}

	public function temp()
	{
		print $this->dj->calculate_votes($this->session->userdata("site_id"));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/dj_json.php */
