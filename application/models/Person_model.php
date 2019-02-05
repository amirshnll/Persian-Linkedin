<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Person_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $firstname, $lastname, $country_id, $zip_code, $birthday, $biography)
    {
    	if(empty($user_id) || empty($firstname) || empty($lastname))
    		return false;

    	$data = array(
    		'user_id'	=>	$user_id,
    		'firstname'	=>	$firstname,
    		'lastname'	=>	$lastname,
    		'country_id'=>	$country_id,
    		'zip_code'	=>	$zip_code,
    		'birthday'	=>	$birthday,
    		'biography'	=>	$biography,
    	);
    	
    	if($this->db->insert('person', $data))
    		return true;
    	else
    		return false;
    }

    public function update($user_id, $firstname, $lastname, $country_id, $zip_code, $birthday)
    {
        if(empty($user_id))
            return false;

        $data = array(
            'firstname' =>  $firstname,
            'lastname'  =>  $lastname,
            'country_id'=>  $country_id,
            'zip_code'  =>  $zip_code,
            'birthday'  =>  $birthday
        );

        $this->db->where('user_id', $user_id);
        if($this->db->update('person', $data))
            return true;
        else
            return false;
    }

    public function read_user_person($user_id)
    {
        if(empty($user_id))
            return false;

        $this->db->limit(1);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('person');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0];
            return $query;
        }
        else
            return false;
    }

    public function read_user_biography($user_id)
    {
        if(empty($user_id))
            return false;

        $this->db->limit(1);
        $this->db->select('biography');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('person');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0];
            return $query;
        }
        else
            return false;
    }

    public function update_user_biography($user_id, $biography)
    {
        if(empty($user_id))
            return false;

        $data = array(
            'biography'  =>  $biography
        );

        $this->db->where('user_id', $user_id);
        if($this->db->update('person', $data))
            return true;
        else
            return false;
    }

    public function read_all_person()
    {
        $query = $this->db->get('person');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }

    public function random_person_country($country_id, $limit)
    {
        if(empty($country_id))
            return false;

        if($limit!=0)
            $this->db->limit($limit);
        $this->db->order_by('id', 'RANDOM');
        $this->db->where('country_id', $country_id);
        $query = $this->db->get('person');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }

    public function random_person_names_like($like_string, $limit)
    {
        if(empty($like_string))
            return false;

        if($limit!=0)
            $this->db->limit($limit);
        $this->db->order_by('id', 'RANDOM');
        $this->db->like('firstname', $like_string);
        $this->db->or_like('lastname', $like_string);
        $this->db->select('*');
        $this->db->from('person');
        $query = $this->db->get('');

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