<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Profile_view_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function viewed_profile_count($user_id)
    {
    	if(empty($user_id))
            return false;
        
        $this->db->where('user_viewed_id', $user_id);
        $this->db->select('id');
        $query = $this->db->get('profile_view');

        if($query->num_rows())
        {
            return $query->num_rows();
        }
        else
            return false;
    }

    public function insert($user_id, $user_viewed_id, $time)
    {
        if(empty($user_viewed_id))
            return false;

        if(empty($time))
            $time = time();

        $data = array(
            'user_id'           =>  $user_id,
            'user_viewed_id'    =>  $user_viewed_id,
            'time'              =>  $time
        );
        
        if($this->db->insert('profile_view', $data))
            return true;
        else
            return false;
    }
    
}

?>