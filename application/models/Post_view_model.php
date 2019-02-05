<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Post_view_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $post_viewed_id, $time)
    {
    	if(empty($user_id) || empty($post_viewed_id))
    		return false;

        if(empty($time))
            $time = time();

    	$data = array(
    		'user_id'		=>	$user_id,
			'post_viewed_id'=>	$post_viewed_id,
			'time'			=>	$time
    	);
    	
    	if($this->db->insert('post_view', $data))
    		return true;
    	else
    		return false;
    }

}

?>