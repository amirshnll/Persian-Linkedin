<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class File_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $filename, $time, $status, $type, $user_agent)
    {
    	if(empty($user_id) || empty($filename) || empty($status) || empty($type) || empty($user_agent))
    		return false;

        if(empty($time))
            $time = time();

    	/* Type 1:images 2:videos */
    	$data = array(
    		'user_id'		=>	$user_id,
			'filename'		=>	$filename,
			'time'			=>	$time,
			'status'		=>	$status,
			'type'			=>	$type,
			'user_agent'	=>	$user_agent
    	);
    	
    	if($this->db->insert('file', $data))
    		return true;
    	else
    		return false;
    }

    public function find_id_by_file_name($filename)
    {
		if(empty($filename))
            return false;

    	$this->db->limit(1);
    	$this->db->where('filename', $filename);
    	$query = $this->db->get('file');

    	if($query->num_rows())
		{
			$query = $query->result_array();
			$query = $query[0]['id'];
			return $query;
		}
		else
			return false;
    }

    public function find_file($id)
    {
        if(empty($id))
            return false;

        $this->db->limit(1);
        $this->db->where('status', 1);
        $this->db->where('id', $id);
        $query = $this->db->get('file');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0]['filename'];
            return $query;
        }
        else
            return false;
    }
    
}

?>