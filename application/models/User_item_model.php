<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class User_item_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function read_user_special_type_item($user_id, $type)
    {
        if(empty($user_id) || empty($type))
            return false;

        /* 
        	Type List : 
    			1. experience
    			2. education
    			3. skills
    			4. project
        */

        $this->db->where('status', 1);
        $this->db->where('type', $type);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('user_item');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }

    public function read_single_special_type_item($user_id, $type, $id)
    {
        if(empty($user_id) || empty($type) || empty($id))
            return false;

        /* 
            Type List : 
                1. experience
                2. education
                3. skills
                4. project
        */

        $this->db->limit(1);
        $this->db->where('id', $id);
        $this->db->where('status', 1);
        $this->db->where('type', $type);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('user_item');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query[0];
        }
        else
            return false;
    }

    public function insert($user_id, $type, $title, $content, $start_date, $end_date, $time)
    {
        if(empty($user_id) || empty($type) || empty($title))
            return false;

        $data = array(
            'user_id'   =>  $user_id,
            'type'      =>  $type,
            'title'     =>  $title,
            'content'   =>  $content,
            'start_date'=>  $start_date,
            'end_date'  =>  $end_date,
            'time'      =>  $time,
            'status'    =>  1
        );
        
        if($this->db->insert('user_item', $data))
            return true;
        else
            return false;

    }

    public function update($user_id, $id, $title, $content, $start_date, $end_date)
    {
        if(empty($user_id) || empty($title))
            return false;

        $data = array(
            'title'     =>  $title,
            'content'   =>  $content,
            'start_date'=>  $start_date,
            'end_date'  =>  $end_date,
        );

        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        if($this->db->update('user_item', $data))
            return true;
        else
            return false;

    }

    public function delete($user_id, $id)
    {
        if(empty($user_id) || empty($id))
            return false;

        $data = array(
            'status'     =>  0
        );

        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        if($this->db->update('user_item', $data))
            return true;
        else
            return false;
    }
    
}

?>