<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dj extends CI_Model {
 
 	//DEFINE API KEY 

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function mark_queued($tracks, $site_id)
    {
		foreach ($tracks as $key) 
		{
			$this->db->where("site_id", $site_id)->where('key', $key)->update('requests', array('queued' => 1));
		}    	
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
}