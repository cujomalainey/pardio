<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voter_json extends CI_Controller {

	/**
	 * Backend Controller
	 * Works strictly with AJAX calls
	 * for voter interface
	 * 
	 */

	function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->model("voter");
        $this->load->library('session');
    }

	public function check_code($code)
	{

		$code = urldecode($code);

		if ($this->voter->check_code($code))
		{
			$this->send(true);
			$voter_id = $this->session->userdata('voter_id');
			if ($voter_id != FALSE && $this->voter->voter_exist($voter_id)) //if user has preset id and check if id is still valid
			{
				//if still valid then update info
				$id = $this->voter->get_id_by_tag($code);
				$this->session->set_userdata(array('site_id' => $id));
				$this->voter->update_site($id, $voter_id);
			}
			else
			{
				//issue new credentials
				$id = $this->voter->get_id_by_tag($code);
				$this->session->set_userdata(array('site_id' => $id, 'voter_id' => $this->voter->add_voter($id)));
			}
		}
		else
		{
			$this->send(FALSE);
		}
	}

	public function search($query)
	{
		$query = urldecode($query);
		$this->load->model('rdio');
		$this->send($this->rdio->search($query));
	}

	public function submit($key)
	{
		$this->load->model(array('rdio', 'common'));
		$site_id 	= $this->session->userdata('site_id');
		$voter_id 	= $this->session->userdata('voter_id');
		if ($site_id != FALSE && $this->rdio->check_key($key) && $this->voter->voter_exist($voter_id))
		{
			$timestamp = $this->voter->last_request_time($voter_id);
			if ($timestamp == "-1" || $timestamp < strtotime("-5 minutes"))
			{
				if ($this->voter->already_requested($key, $site_id))
				{
					$this->common->request($key, $voter_id, $site_id);
					$this->send("0"); //Everything went ok
				}	
				else
				{
					$this->send(array(3, "This song has been requested in the past 2 hours already")); 
				}
			}
			else
			{
				$this->send(array(2, "It has been to soon since your last request")); 
			}
		}
		else
		{
			$this->send(array(1, "Sorry there was an error")); 
			//that key or the voter doesnt exist, or site
		}
	}

	public function vote($key, $dir)
	{
		$this->load->model('rdio');
		$voter_id = $this->session->userdata('voter_id');
		if ($this->session->userdata('site_id') != FALSE && $this->rdio->check_key($key) && $this->voter->voter_exist($voter_id))
		{
			$this->voter->vote($key, $voter_id, $dir);
			$this->send(0, "Vote Cast");
		}
		else
		{
			$this->send(array(1, "Sorry there was an error"));
		}
		
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
				$this->send(array('total' => $this->voter->active_members($site_id), 'name' => $this->voter->get_name_by_id($site_id), 'queue' => $this->voter->get_queue($site_id), 'hash' => $this->common->get_queue_hash($site_id), 'nowPlaying' => $this->voter->now_playing($site_id), 'votes' => $this->voter->get_votes($this->session->userdata('voter_id'))));
				break;
			}
			$i++;
		}
	}

	public function exit_site()
	{
		$this->voter->delete_votes($this->session->userdata('voter_id'));
		$this->session->unset_userdata(array("site_id" => ""));
		$this->output->set_header("Location: http://wizuma.com");
	}

	public function qr_code($site_id)
	{
		$this->load->model('voter');
		if ($this->voter->check_key($site_id))
		{
			$voter_id = $this->session->userdata('voter_id');
			if ($voter_id != FALSE && $this->voter->voter_exist($voter_id)) //if user has preset id and check if id is still valid
			{
				//if still valid then update info
				$this->session->set_userdata(array('site_id' => $site_id));
				$this->voter->update_site($site_id, $voter_id);
			}
			else
			{
				//issue new credentials
				$this->session->set_userdata(array('site_id' => $site_id, 'voter_id' => $this->voter->add_voter($site_id)));
			}
			$this->output->set_header("Location: http://wizuma.com/index.php/voter/party");
		}
		else
		{
			$this->output->set_header("Location: http://wizuma.com/");
		}
	}

	private function send($data)
	{
		$this->output->set_output(json_encode($data));
	}
}

/* End of file voter_json.php */
/* Location: ./application/controllers/voter_json.php */