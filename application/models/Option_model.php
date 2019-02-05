<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Option_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }
    
    private function check_option_name($value)
    {
        if(empty($value))
            return false;

        switch ($value) {
            case 'temp_lock':
                return true;
                break;
            default :
                return false;
        }
        return false;
    }

    public function set_option($name, $value)
    {
        if(empty($name))
            return false;

        if(!$this->check_option_name($name))
            return false;

        $data = array(
            'value'  =>  $value
        );

        $this->db->where('name', $name);
        if($this->db->update('option', $data))
            return true;
        else
            return false;
    }

    public function get_option($name)
    {
        if(empty($name))
            return false;

        if(!$this->check_option_name($name))
            return false;

        $this->db->limit(1);
        $this->db->where('name', $name);
        $this->db->select('value');
        $query = $this->db->get('option');

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