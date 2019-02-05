<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Like_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $post_id, $time)
    {
    	if(empty($user_id) || empty($post_id) || empty($time))
    		return false;

    	$data = array(
    		'user_id'		=>	$user_id,
			'post_id'		=>	$post_id,
			'time'			=>	$time,
			'status'		=>	1
    	);
    	
    	if($this->db->insert('like', $data))
    		return true;
    	else
    		return false;
    }

    public function set_post_status_zero($user_id, $post_id)
    {
    	if(empty($user_id) || empty($post_id))
            return false;

        $data = array(
            'status'  =>  0
        );

        $this->db->where('user_id', $user_id);
        $this->db->where('post_id', $post_id);
        if($this->db->update('like', $data))
            return true;
        else
            return false;
    }
    
}

?>