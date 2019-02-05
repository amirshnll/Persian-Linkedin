<?php

/**
 * Created : 09/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Old_password_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $password, $time, $user_agent)
    {
    	if(empty($user_id) || empty($password) || empty($time) || empty($user_agent))
    		return false;

    	$data = array(
    		'user_id'	=>	$user_id,
    		'password'	=>	$password,
    		'time'		=>	$time,
    		'user_agent'=>	$user_agent
    	);
    	
    	if($this->db->insert('old_password', $data))
    		return true;
    	else
    		return false;
    }

}

?>