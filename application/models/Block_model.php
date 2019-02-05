<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Block_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function is_block($source_user_id, $destination_user_id)
    {
    	if(empty($source_user_id) || empty($destination_user_id))
    		return false;

    	$this->db->limit(1);
    	$this->db->where('status', 1);
    	$this->db->where('user_blocked_id', $destination_user_id);
    	$this->db->where('user_id', $source_user_id);
    	$query = $this->db->get('block');

    	if($query->num_rows())
		{
			return true;
		}
		else
			return false;
    }

    public function block($source_user_id, $destination_user_id, $time)
    {
    	if(empty($source_user_id) || empty($destination_user_id))
    		return false;

    	$data = array(
    		'user_id'			=>	$source_user_id,
    		'user_blocked_id'	=>	$destination_user_id,
    		'time'				=>	$time,
    		'status'			=>	1
    	);
    	
    	if($this->db->insert('block', $data))
    		return true;
    	else
    		return false;
    }

    public function unblock($source_user_id, $destination_user_id)
    {
    	if(empty($source_user_id) || empty($destination_user_id))
    		return false;

        $data = array(
            'status'     =>  0
        );

        $this->db->where('user_blocked_id', $destination_user_id);
    	$this->db->where('user_id', $source_user_id);
        if($this->db->update('block', $data))
            return true;
        else
            return false;
    }
    
}

?>