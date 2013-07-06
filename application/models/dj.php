<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dj extends CI_Model {
 
 	//DEFINE API KEY 

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_unqueued_tracks($site_id)
    {
    	$query = $this->db->select('key')->where('queued', 0)->where('site_id', $site_id)->get('requests');
    	return $query->result_array();
    }

    public function mark_queued($tracks, $site_id)
    {
		foreach ($tracks as $key) 
		{
			$this->db->where("site_id", $site_id)->where('key', $key)->update('requests', array('queued' => 1));
		}    	
    }
}