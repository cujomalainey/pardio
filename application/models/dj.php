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
		//mark request table with whether the song is streamable or not in this area
        $this->db->where("site_id", $site_id)->where('key', $key)->update('requests', array('can_stream' => $streamable));
        if ($streamable == 0)
        {
            $this->db->where("site_id", $site_id)->where('key', $key)->update('requests', array('drop' => 1));
        }
        //send message too    	
    }

    public function delete_request($key, $site_id)
    {
        //mark delete as true on request table, returns name of deleted song
        $this->db->where('key', $key)->where('site_id', $site_id)->where('played', 0)->update('requests', array('drop' => 1));
        $this->load->model('common');
        $this->common->update_queue_hash($site_id);
        return $this->db->select('name')->where('key', $key)->get('songs')->row()->name;
    }

    public function played($key, $site_id)
    {
        //marks current song as played
        $this->db->where('site_id', $site_id)->where('key', $key)->where('drop', 0)->update('requests', array('played' => 1));
    }

    public function get_queue($site_id)
    {
        //build queue using left join from cache and requests table
        $query = $this->db->where('site_id', $site_id)->where('played', '0')->where('drop', 0)->where('can_stream !=', "0")->or_where('can_stream IS NULL')->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit, can_stream")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        $redo = false;
        $this->load->model('common');
        //if any names return null then fill them into the cache then rebuild table
        foreach ($query->result() as $row)
        {
            if (is_null($row->name))
            {
                $redo = true;
                $this->common->add_song_to_cache($row->key);
            }
        }
        //if nulls found requery database
        if ($redo == true)
        {
            $query = $this->db->where('site_id', $site_id)->where('played', '0')->where('drop', 0)->from("requests")->select("requests.key, name, icon_url, artist, album, is_explicit")->order_by("order", "ASC")->join('songs', 'songs.key = requests.key', 'left')->get();
        }
        return $query->result_array();
    }

    public function calculate_votes($site_id)
    {
        //Get lowest not played order index
        $start_index = $this->db->select("order")->where("played", "0")->where('can_stream !=', "0")->or_where('can_stream IS NULL')->where("site_id", $site_id)->order_by("order", "ASC")->get("requests", 1, 0)->row()->order;
        //If index not found set to 1
        if (is_null($start_index))
        {
            $start_index = 1;
        }
        //Find all tracks for site, count votes, check time added
        $query = $this->db->select("key, vote, added, count(*)")->from("votes")->where("played", 0)->where('can_stream !=', "0")->or_where('can_stream IS NULL')->where("site_id", $site_id)->join('requests', 'requests.id = votes.request_id', 'right')->group_by(array('key','vote'))->get()->result_array();
        //order results into an indexed array while looking for one with the highest vote value
        $votes = array();
        $high_vote = array("key" => "", "value" => ""); //contains duplicate info of site with highest vote value
        //tally votes
        foreach ($query as $index) 
        {
            //if index not set, then set it
            if (isset($votes[$index['key']]['value']) == FALSE) 
            {
                $votes[$index['key']]['value'] = 0;
            }
            //add up votes
            if ($index['vote'] == 1)
            {
                $votes[$index['key']]['value'] += $index['count(*)'];
            }
            elseif (is_null($index['vote']) == FALSE)
            {
                $votes[$index['key']]['value'] -= $index['count(*)'];
            }
            //caluculate time difference since song was added
            $votes[$index['key']]['time'] = strtotime(date('c')) - strtotime($index['added']);
            //check if the song has the highest vote value
            if ($high_vote['value'] == "" || $high_vote['value'] < $votes[$index['key']]['value'])
            {
                $high_vote['key'] = $index['key'];
                $high_vote['value'] = $votes[$index['key']]['value'];
                $high_vote['time'] = $votes[$index['key']]['time'];
            }
        }
        //if the the song with highest vote value is positive then calculate a temp, otherwise set to 1
        if ($high_vote['value'] > 0)
        {
            $coefficient = $high_vote['time']/(0.5*$high_vote['value']);
        }
        else
        {
            $coefficient = 1;
        }
        //turn results into a sortable set of arrays   
        $key = array();
        $vote_value = array();
        foreach ($votes as $name => $value) {
            array_push($key, $name);
            //vote value calculation, derived from pythagorean theorem
            array_push($vote_value, sqrt(pow($value['time'], 2) + pow($value['value'] * $coefficient, 2))); 
        }
        array_multisort($vote_value, SORT_DESC, $key);
        $data = array();
        //build batch update array
        foreach ($key as $ind => $value) {
            array_push($data, array("key" => $value, "order" => $ind + $start_index));
        }
        //update db
        $this->db->update_batch('requests', $data, 'key');
        $this->load->model('common');
        $this->common->update_queue_hash($site_id);
    }
}