<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Message_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function is_unread($user_sender_id, $user_reciver_id)
    {
        if(empty($user_sender_id) || empty($user_reciver_id))
            return false;

        $this->db->limit(1);
        $this->db->where('status', 1);
        $this->db->where('unread', 1);
        $this->db->where("(user_sender_id='" . $user_reciver_id . "' AND user_reciver_id='" . $user_sender_id . "')", NULL, FALSE);
        $query = $this->db->get('message');

        if($query->num_rows())
        {
            return true;
        }
        else
            return false;

    }

    public function active_chat_user($user_id)
    {
        if(empty($user_id))
            return false;

        $this->db->order_by('time', 'DESC');
        $this->db->where('status', 1);
        $this->db->where("(user_sender_id='" . $user_id . "' OR user_reciver_id='" . $user_id . "')", NULL, FALSE);
        $this->db->group_by(array('user_reciver_id', 'user_sender_id'));
        $query = $this->db->get('message');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }

    public function load_chat($user_sender_id, $user_reciver_id)
    {
        if(empty($user_sender_id) || empty($user_reciver_id))
            return false;

        $this->db->where("(user_sender_id='" . $user_reciver_id . "' AND user_reciver_id='" . $user_sender_id . "')", NULL, FALSE);
        $this->db->where('status', 1);
        $data = array(
            'unread'            =>  0
        );
        $this->db->update('message', $data);

    	$this->db->order_by('id', 'ASC');
    	$this->db->where('status', 1);
    	$this->db->where("(user_sender_id='" . $user_sender_id . "' AND user_reciver_id='" . $user_reciver_id . "')", NULL, FALSE);
        $this->db->or_where("(user_sender_id='" . $user_reciver_id . "' AND user_reciver_id='" . $user_sender_id . "')", NULL, FALSE);
    	$query = $this->db->get('message');

    	if($query->num_rows())
		{
			$query = $query->result_array();
			return $query;
		}
		else
			return false;
    }

    public function insert($user_sender_id, $user_reciver_id, $content, $time, $status)
    {
    	if(empty($user_sender_id) || empty($user_reciver_id) || empty($content) || empty($status))
    		return false;

        if(empty($time))
            $time = time();

    	$data = array(
    		'user_sender_id'	=>	$user_sender_id,
    		'user_reciver_id'	=>	$user_reciver_id,
    		'content'			=>	$content,
    		'time'				=>	$time,
    		'status'			=>	$status,
            'unread'            =>  1
    	);
    	
    	if($this->db->insert('message', $data))
    		return true;
    	else
    		return false;
    }
    
}

?>