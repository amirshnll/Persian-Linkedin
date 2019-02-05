<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Connections_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function user_connection($user_id, $limit=0)
    {
    	if(empty($user_id))
            return false;

        if(!is_numeric($limit))
        	$limit = 0;

        if($limit!=0)
        	$this->db->limit($limit);
        $this->db->where('user.status', 1);
        $this->db->where('connections.status', 1);
        $this->db->where('connections.connect_id', $user_id);
        $this->db->select('user.id, person.firstname, person.lastname');
        $this->db->from('connections');
		$this->db->join('user', 'user.id = connections.connected_id');
		$this->db->join('person', 'person.user_id = connections.connected_id');
        $query = $this->db->get('');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }

    public function user_connection_count($user_id)
    {
    	if(empty($user_id))
            return false;

        $this->db->where('user.status', 1);
        $this->db->where('connections.status', 1);
        $this->db->where('connections.connect_id', $user_id);
        $this->db->select('connections.id');
        $this->db->from('connections');
		$this->db->join('user', 'user.id = connections.connected_id');
        $query = $this->db->get('');

        if($query->num_rows())
        {
            return $query->num_rows();
        }
        else
            return false;
    }
    
}

?>