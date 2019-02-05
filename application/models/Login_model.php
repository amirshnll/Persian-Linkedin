<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Login_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $time, $user_agent)
    {
    	if(empty($user_id) || empty($user_agent))
    		return false;

        if(empty($time))
            $time = time();

    	$data = array(
    		'user_id'	=>	$user_id,
    		'time'		=>	$time,
    		'user_agent'=>	$user_agent
    	);
    	
    	if($this->db->insert('login', $data))
    		return true;
    	else
    		return false;
    }
    
}

?>