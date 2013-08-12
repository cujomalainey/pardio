<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dj extends CI_Model {
 
 	//DEFINE API KEY 

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function mark_stream($key, $streamable, $site_id)
    {
		$this->db->where("site_id", $site_id)->where('key', $key)->update('requests', array('can_stream' => $streamable));    	
    }

    public function delete_request($key, $site_id)
    {
        $this->db->where('key', $key)->where('site_id', $site_id)->where('played', 0)->update('requests', array('drop' => 1));
        $this->load->model('common');
        $this->common->update_queue_hash($site_id);
        return $this->db->select('name')->where('key', $key)->get('songs')->row()->name;
    }

    public function remove($key, $site_id)
    {
        $this->db->where('site_id', $site_id)->where('key', $key)->where('played', 0)->update('requests', array('drop' => 1));
    }

    public function played($key, $site_id)
    {
        $this->db->where('site_id', $site_id)->where('key', $key)->where('drop', 0)->update('requests', array('played' => 1));
    }

    public function get_queue($site_id)
    {
        //rebuild using temp tables
        $query = $this->db->where('site_id', $site_id)->where('played', '0')->where('drop', 0)->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        $redo = false;
        $this->load->model('common');
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
            $query = $this->db->where('site_id', $site_id)->where('played', '0')->where('drop', 0)->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        }
        return $query->result_array();
    }
}