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
        //TODO use timestamp on request rather than actual users activity timestamp
        $query = $this->db->select('added')->where('voter_id', $voter_id)->order_by('added', 'DESC')->get('requests', 1, 0);
        if ($query->num_rows() >= 1)
        {
            return strtotime($query->row()->added); 
        }
        else
        {
            return "-1";
        }
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
        $query = $this->db->select('vote, request_id')->join("requests", "votes.request_id = requests.id")->where("votes.voter_id", $voter_id)->where("requests.key", $key)->get("votes");
        $query2 = $this->db->select('id')->where("site_id", $this->session->userdata('site_id'))->where("key", $key)->get("requests");
        $row = $query2->row();
        if ($query->num_rows() >= 1)
        {
            $this->db->where("voter_id", $voter_id)->where("request_id", $row->id)->update('votes', array('vote' => $dir));
        }
        else
        {
            $this->db->insert('votes', array('voter_id' => $voter_id, 'request_id' => $row->id, 'vote' => $dir));
        }
    }

    public function now_playing($site_id)
    {
        $query = $this->db->where('site_id', $site_id)->where('played', 1)->where('drop', 0)->select("name, icon_url, artist, album, is_explicit")->order_by("order", "DESC")->join('songs', 'songs.key = requests.key', 'left')->get("requests", 1, 0);
        return $query->result_array();
    }

    public function get_queue($site_id)
    {
        $this->load->model('common');
        $query = $this->db->where('site_id', $site_id)->where('played', '0')->where('drop', 0)->where('can_stream', 1)->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        $redo = false;
        foreach ($query->result() as $row)
        {
            if (is_null($row->name))
            {
                $redo = true;
                $this->common->add_song_to_cache($row->key);
            }
        }
        if ($redo == true)
        {
            $query = $this->db->where('site_id', $site_id)->where('played', '0')->where('drop', 0)->where('can_stream', 1)->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        }
        return $query->result_array();
    }
}