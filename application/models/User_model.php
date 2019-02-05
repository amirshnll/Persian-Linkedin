<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class User_model extends CI_Model {

	private function encrypt_password($value)
	{
		return sha1(md5($value) . 'plnk');
	}

	private function is_unique_email($email)
	{
        if(empty($email))
            return false;

		$this->db->limit(1);
    	$this->db->where('email', $email);
    	$this->db->select('id');
    	$query = $this->db->get('user');

    	if($query->num_rows())
			return false;
		else
			return true;
	}

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }
    
    public function insert($email, $password, $register_time, $type, $status)
    {
    	if(empty($email) || empty($password) || empty($register_time) || empty($type) || empty($status))
    		return false;

    	if(!$this->is_unique_email($email))
    		return false;

    	/* Type 1 : Admin, Type 2 : User */
    	$data = array(
    		'email'			=>	$email,
    		'password'		=>	$this->encrypt_password($password),
    		'register_time'	=>	$register_time,
    		'type'			=>	$type,
    		'status'		=>	$status
    	);
    	
    	if($this->db->insert('user', $data))
    		return true;
    	else
    		return false;
    }

    public function get_id_by_email($email)
    {
        if(empty($email))
            return false;

    	$this->db->limit(1);
    	$this->db->where('email', $email);
    	$this->db->select('id');
    	$query = $this->db->get('user');

    	if($query->num_rows())
		{
			$query = $query->result_array();
			$query = $query[0]['id'];
			return $query;
		}
		else
			return false;
    }

    public function get_type_by_id($id)
    {
        if(empty($id))
            return false;

    	$this->db->limit(1);
    	$this->db->where('id', $id);
    	$this->db->select('type');
    	$query = $this->db->get('user');

    	if($query->num_rows())
		{
			$query = $query->result_array();
			/* Type 1 : Admin, Type 2 : User */
			$query = $query[0]['type'];
			return $query;
		}
		else
			return false;
    }

    public function get_user_by_id($id)
    {
        if(empty($id))
            return false;

    	$this->db->limit(1);
    	$this->db->where('id', $id);
    	$query = $this->db->get('user');

    	if($query->num_rows())
		{
			$query = $query->result_array();
			$query = $query[0];
			return $query;
		}
		else
			return false;
    }

    public function get_password_by_id($id)
    {
        if(empty($id))
            return false;

        $this->db->limit(1);
        $this->db->where('id', $id);
        $this->db->select('password');
        $query = $this->db->get('user');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $query = $query[0]['password'];
            return $query;
        }
        else
            return false;
    }

    public function user_enable($id)
    {
        if(empty($id))
            return false;

    	$this->db->limit(1);
    	$this->db->where('id', $id);
    	$this->db->select('status');
    	$query = $this->db->get('user');

    	if($query->num_rows())
		{
			$query = $query->result_array();
			$query = $query[0]['status'];
			if($query)
				return true;
			else
				return false;
		}
		else
			return false;
    }

    public function user_login($email, $password, $type)
    {
        if(empty($email) || empty($password) || empty($type))
            return false;

    	$this->db->limit(1);
    	$this->db->where('email', $email);
    	$this->db->where('password', $this->encrypt_password($password));
    	$this->db->where('status', 1);
    	/* Type 1 : Admin, Type 2 : User */
    	$this->db->where('type', $type);
    	$this->db->select('id');
    	$query = $this->db->get('user');

    	if($query->num_rows())
			return true;
		else
			return false;
    }

    public function change_password($id, $new_password)
    {
        if(empty($id) || empty($new_password))
            return false;

        $data = array(
            'password'  =>  $this->encrypt_password($new_password)
        );

        $this->db->where('id', $id);
        if($this->db->update('user', $data))
            return true;
        else
            return false;
    }

    public function authorize_password($id, $password)
    {
        if(empty($id) || empty($password))
            return false;

        $this->db->limit(1);
        $this->db->where('id', $id);
        $this->db->where('password', $this->encrypt_password($password));
        $this->db->select('id');
        $query = $this->db->get('user');

        if($query->num_rows())
            return true;
        else
            return false;
    }

    public function find_profile($string_find)
    {
        if(empty($string_find))
            return false;

        $this->db->where('status', 1);
        $query = $this->db->get('user');

        if($query->num_rows())
        {
            $query = $query->result_array();
            $result = null;
            
            foreach ($query as $qq) {
                if(md5($qq['id']) == $string_find)
                {
                    $result = $qq;
                    break;
                }
            }

            if(is_null($result))
                return false;
            else
                return $result;
        }
        else
            return false;
    }

}

?>