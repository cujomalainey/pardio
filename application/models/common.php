<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_queue($site_id)
    {
        //rebuild using temp tables
        $query = $this->db->where('site_id', $site_id)->where('played', '0')->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit, queued")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        $redo = false;
        foreach ($query->result() as $row)
        {
            if (is_null($row->name))
            {
                $redo = true;
                $this->add_song_to_cache($row->key);
            }
        }
        $query = $this->db->where('site_id', $site_id)->where('played', '0')->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        return $query->result_array();
    }

    public function update_queue_hash($site_id)
    {
    	$query = $this->db->where('site_id', $site_id)->where('played', '0')->from("requests")->select("key")->order_by("order", "ASC")->get();
    	$hash = md5($query);
    	$this->db->where('id', $site_id)->update('sites', array('queue_hash' => $hash));
    }

    public function get_queue_hash($site_id)
    {
    	$query = $this->db->select('queue_hash')->where('id', $site_id)->from('sites')->get();
    	return $query->row()->queue_hash;
    }

    public function request($key, $voter_id, $site_id)
    {
        $this->db->where('id', $voter_id)->update('voters', array('last_active' => date('Y-m-d H:i:s')));
        if ($this->db->where('key', $key)->select('is_explicit')->from('songs')->get()->num_rows() == 0)
        {
           $this->add_song_to_cache($key);
        }
        else
        {
            $this->db->where('key', $key)->update('songs', array('last_used' => date('Y-m-d H:i:s')));
        }
        $query = $this->db->select('order')->where('site_id', $site_id)->order_by('order', 'DESC')->get('requests', 1, 0);
        if ($query->num_rows() == 0)
        {
            $this->db->insert('requests', array('site_id' => $site_id, 'voter_id' => $voter_id, 'key' => $key, 'order' => 0));
        }
        else
        {
            $this->db->insert('requests', array('site_id' => $site_id, 'voter_id' => $voter_id, 'key' => $key, 'order' => $query->row()->order + 1));
        }
    }

    private function add_song_to_cache($key)
    {
        $this->load->model('rdio');
        $song = $this->rdio->get_song_by_key($key);
        $song = $song->result->$key;
        $this->db->insert('songs', array('name' => $song->name, 'icon_url' => $song->icon, 'artist' => $song->artist, 'album' => $song->album, 'is_explicit' => $song->isExplicit, 'key' =>$song->key));
    }
}