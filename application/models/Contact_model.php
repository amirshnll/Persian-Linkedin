<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Contact_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $type, $content, $time)
    {
    	if(empty($user_id) || empty($type))
    		return false;
        
        if(empty($time))
            $time = time();

    	/* 
        	Type List : 
    			1. Linkedin
    			2. Twitter
    			3. Telegram
    			4. Skype
        */

    	$data = array(
    		'user_id'	=>	$user_id,
    		'type'		=>	$type,
    		'content'	=>	$content,
    		'time'		=>	$time,
    		'status'	=>	1
    	);
    	
    	if($this->db->insert('contact', $data))
    		return true;
    	else
    		return false;
    }

    public function update($user_id, $type, $content)
    {
        if(empty($user_id) || empty($type))
            return false;

        /* 
            Type List : 
                1. Linkedin
                2. Twitter
                3. Telegram
                4. Skype
        */

        $data = array(
            'content' =>  $content,
        );

        $this->db->where('status', 1);
        $this->db->where('type', $type);
        $this->db->where('user_id', $user_id);
        if($this->db->update('contact', $data))
            return true;
        else
            return false;
    }

    public function user_all_contact($user_id)
    {
    	if(empty($user_id))
            return false;

        $this->db->where('status', 1);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('contact');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }
    
}

?>