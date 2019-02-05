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
        $this->db->select('user.id, person.firstname, person.lastname, connections.connected_id');
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

    public function user_respondconnection($user_id, $limit=0)
    {
        if(empty($user_id))
            return false;

        if(!is_numeric($limit))
            $limit = 0;

        if($limit!=0)
            $this->db->limit($limit);
        $this->db->where('user.status', 1);
        $this->db->where('connections.status', 2);
        $this->db->where('connections.requester', -1);
        $this->db->where('connections.connect_id', $user_id);
        $this->db->select('user.id, person.firstname, person.lastname, connections.connected_id');
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

    public function is_connection($user_id, $connect_id)
    {
        if(empty($user_id) || empty($connect_id))
            return false;

        $this->db->limit(1);
        $this->db->where('status', 1);
        $this->db->where('connected_id', $connect_id);
        $this->db->where('connect_id', $user_id);
        $query = $this->db->get('connections');

        if($query->num_rows())
        {
            return true;
        }
        else
            return false;
    }

    public function is_respond_connection($user_id, $connect_id)
    {
        if(empty($user_id) || empty($connect_id))
            return false;

        $this->db->limit(1);
        $this->db->where('status', 2);
        $this->db->where('connected_id', $connect_id);
        $this->db->where('connect_id', $user_id);
        $query = $this->db->get('connections');

        if($query->num_rows())
        {
            return true;
        }
        else
            return false;
    }

    public function is_requester_connection($user_id, $connect_id)
    {
        if(empty($user_id) || empty($connect_id))
            return false;

        $this->db->limit(1);
        $this->db->where('status', 2);
        $this->db->where('connected_id', $connect_id);
        $this->db->where('connect_id', $user_id);
        $query = $this->db->get('connections');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0]['requester'];
            if($query==1)
                return true;
            else
                return false;
        }
        else
            return false;
    }

    public function insert($connect_id, $connected_id, $time, $status, $requester)
    {
        if(empty($connect_id) || empty($connected_id) || empty($status) || empty($requester))
            return false;

        if(empty($time))
            $time = time();

        $data = array(
            'connect_id'    =>  $connect_id,
            'connected_id'  =>  $connected_id,
            'time'          =>  $time,
            'status'        =>  $status,
            'requester'     =>  $requester
        );
        
        if($this->db->insert('connections', $data))
            return true;
        else
            return false;
    }

    public function confirm_connection($user_id, $connect_id)
    {
        if(empty($user_id) || empty($connect_id))
            return false;

        $data = array(
            'status'     =>  1
        );

        $this->db->where('status', 2);
        $this->db->where('connected_id', $connect_id);
        $this->db->where('connect_id', $user_id);
        if($this->db->update('connections', $data))
            return true;
        else
            return false;
    }

    public function delete_connection($user_id, $connect_id)
    {
        if(empty($user_id) || empty($connect_id))
            return false;

        $data = array(
            'status'     =>  0
        );

        $this->db->where('connected_id', $connect_id);
        $this->db->where('connect_id', $user_id);
        if($this->db->update('connections', $data))
            return true;
        else
            return false;
    }
    
}

?>