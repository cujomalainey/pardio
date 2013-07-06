<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voter extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function check_code($code)
    {
    	//checks to see if code exists
    	$query = $this->db->get_where('sites', array('tag' => $code));
    	if ($query->num_rows() >= 1)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    public function get_id_by_tag($tag)
    {
        $query = $this->db->select('id')->get_where('sites', array('tag' => $tag));
        $row = $query->row();
        return $row->id;
    }

    public function active_members($id)
    {
        //ADD TIME CHECK AND FILTER INACTIVE VOTERS
        return $this->db->where('site_id', $id)->count_all_results('voters');
    }

    public function get_name_by_id($id)
    {
        $query = $this->db->select('name')->get_where('sites', array('id' => $id));
        $row = $query->row();
        return $row->name;
    }

    public function update_site($site_id, $voter_id)
    {
        $this->db->where('id', $voter_id)->update('voters', array('site_id' => $site_id));
        $this->db->delete('votes', array('voter_id' => $voter_id));
    }

    public function add_voter($id)
    {
        $this->db->insert('voters', array('site_id' => $id));
        return $this->db->insert_id();
    }

    public function delete_votes($voter_id)
    {
        $this->db->delete('votes', array('voter_id' => $voter_id)); 
    }

    public function voter_exist($voter_id)
    {
        $query = $this->db->where('id', $voter_id)->get('voters');
        if ($query->num_rows() >= 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function last_request_time($voter_id)
    {
        //TODO use timestamp on request rather than actual user
        $query = $this->db->select('last_active')->from('voters')->where('id', $voter_id)->get();
        return strtotime($query->row()->last_active); 
    }

    public function already_requested($key, $site_id)
    {
        //returns true if last reqquest of the song is 2 hours prior
        $query = $this->db->select('added')->where('key', $key)->where('site_id', $site_id)->order_by('added', 'DESC')->get('requests', 1, 0);
        if ($query->num_rows() == 0 || strtotime($query->row()->added) < strtotime("-2 hours"))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function vote($key, $voter_id, $dir)
    {
        $this->db->where('id', $voter_id)->update('voters', array('last_active' => date('Y-m-d H:i:s')));
        $query = $this->db->select('vote')->where("voter_id", $voter_id)->where("key", $key)->get("votes");
        if ($query->num_rows() >= 1)
        {
            $this->db->where("voter_id", $voter_id)->where("key", $key)->update('votes', array('vote' => $dir));
        }
        else
        {
            $this->db->insert('votes', array('voter_id' => $voter_id, 'key' => $key, 'vote' => $dir));
        }
    }
}