<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Report_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function check_duplicate($user_id, $report_id, $type)
    {
    	if(empty($user_id) || empty($report_id))
            return false;

        /* 
        	Type List : 
    			1. Page Report
    			2. Post Report
        */
		$this->db->limit(1);
		$this->db->where('type', 1);
    	$this->db->where('user_reported_id', $report_id);
    	$this->db->where('user_id', $user_id);
    	$query = $this->db->get('report');

    	if($query->num_rows())
			return true;
		else
			return false;
    }

    public function insert($user_id, $report_id, $type, $time)
    {
		if(empty($user_id) || empty($report_id) || empty($type))
    		return false;

        if(empty($time))
            $time = time();

    	$data = array(
    		'user_id'		    =>	$user_id,
    		'user_reported_id'	=>	$report_id,
    		'type'			    =>	$type,
    		'time'			    =>	$time
    	);
    	
    	if($this->db->insert('report', $data))
    		return true;
    	else
    		return false;
    }

    public function report_user_count($user_id, $type)
    {
        if(empty($user_id) || empty($type))
            return 0;

        /* 
            Type List : 
                1. Page Report
                2. Post Report
        */
        $this->db->where('type', 1);
        $this->db->where('user_reported_id', $report_id);
        $query = $this->db->get('report');
        return $query->num_rows();
    }
    
}

?>