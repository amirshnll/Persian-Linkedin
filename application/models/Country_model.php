<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Country_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function select_all()
    {
        $this->db->select('id');
        $this->db->select('name');
        $this->db->where('status', 1);
        $query = $this->db->get('country');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }

    public function get_country_name($id)
    {
    	if(empty($id))
            return false;

    	$this->db->limit(1);
    	$this->db->where('status', 1);
    	$this->db->where('id', $id);
    	$query = $this->db->get('country');

    	if($query->num_rows())
		{
			$query = $query->result_array();
			$query = $query[0]['name'];
			return $query;
		}
		else
			return false;
    }

    public function get_country_id($name)
    {
        if(empty($name))
            return false;

        $this->db->limit(1);
        $this->db->where('status', 1);
        $this->db->where('name', $name);
        $query = $this->db->get('country');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0]['id'];
            return $query;
        }
        else
            return false;
    }
    
}

?>