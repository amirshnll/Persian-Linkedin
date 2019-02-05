<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Post_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }
    
    public function insert($user_id, $file_id, $content, $create_time, $updated_time, $status, $user_agent)
    {
    	if(empty($user_id) || empty($content) || empty($create_time) || empty($updated_time) || empty($status) || empty($user_agent))
    		return false;

    	$data = array(
    		'user_id'		=>	$user_id,
			'file_id'		=>	$file_id,
			'content'		=>	$content,
			'create_time'	=>	$create_time,
			'updated_time'	=>	$updated_time,
			'status'		=>	$status,
			'user_agent'	=>	$user_agent
    	);
    	
    	if($this->db->insert('post', $data))
    		return true;
    	else
    		return false;
    }

    public function post_timeline($limit)
    {
        
    }

}

?>