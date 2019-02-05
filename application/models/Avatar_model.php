<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Avatar_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $filename, $time, $status, $user_agent)
    {
    	if(empty($user_id) || empty($filename) || empty($time) || empty($status) || empty($user_agent))
    		return false;

    	$data = array(
    		'user_id'	=>	$user_id,
    		'filename'	=>	$filename,
    		'time'		=>	$time,
    		'status'	=>	$status,
    		'user_agent'=>	$user_agent
    	);
    	
    	if($this->db->insert('avatar', $data))
    		return true;
    	else
    		return false;
    }

    public function user_current_avatar($user_id)
    {
        if(empty($user_id))
            return false;

        $this->db->limit(1);
        $this->db->where('status', '1');
        $this->db->where('user_id', $user_id);
        $this->db->select('filename');
        $query = $this->db->get('avatar');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0]['filename'];
            return $query;
        }
        else
            return false;
    }

    public function disable_current_avatar($user_id)
    {
        if(empty($user_id))
            return false;

        $data = array(
            'status'  =>  0
        );

        $this->db->where('user_id', $user_id);
        if($this->db->update('avatar', $data))
            return true;
        else
            return false;
    }
    
}

?>