<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class User_option_model extends CI_Model {

    private function check_option_name($value)
    {
        if(empty($value))
            return false;

        switch ($value) {
            case 'private_page':
            case 'private_contact':
            case 'private_avatar':
            case 'need_change_password':
                return true;
                break;
            default :
                return false;
        }
        return false;
    }

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }
    
    public function insert($user_id, $name, $value)
    {
    	if(empty($user_id) || empty($name))
    		return false;

        if(!$this->check_option_name($name))
            return false;

    	$data = array(
    		'user_id'	=>	$user_id,
    		'name'		=>	$name,
    		'value'		=>	$value
    	);
    	
    	if($this->db->insert('user_option', $data))
    		return true;
    	else
    		return false;
    }

    public function set_option($user_id, $name, $value)
    {
        if(empty($user_id) || empty($name))
            return false;

        if(!$this->check_option_name($name))
            return false;

        $data = array(
            'value'  =>  $value
        );

        $this->db->where('name', $name);
        $this->db->where('user_id', $user_id);
        if($this->db->update('user_option', $data))
            return true;
        else
            return false;
    }

    public function get_option($user_id, $name)
    {
        if(empty($user_id) || empty($name))
            return false;

        if(!$this->check_option_name($name))
            return false;

        $this->db->limit(1);
        $this->db->where('name', $name);
        $this->db->where('user_id', $user_id);
        $this->db->select('value');
        $query = $this->db->get('user_option');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0]['value'];
            return $query;
        }
        else
            return false;
    }

}

?>