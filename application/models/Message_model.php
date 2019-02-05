<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Message_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function load_chat()
    {
    	$this->db->where('status', 1);
    	$query = $this->db->get('user');

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