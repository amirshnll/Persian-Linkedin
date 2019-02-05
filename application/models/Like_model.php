<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Like_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }

    public function insert($user_id, $post_id, $time)
    {
    	if(empty($user_id) || empty($post_id))
    		return false;

        if(empty($time))
            $time = time();

    	$data = array(
    		'user_id'		=>	$user_id,
			'post_id'		=>	$post_id,
			'time'			=>	$time,
			'status'		=>	1
    	);
    	
    	if($this->db->insert('like', $data))
    		return true;
    	else
    		return false;
    }

    public function delete($user_id, $post_id)
    {
    	if(empty($user_id) || empty($post_id))
            return false;

        $data = array(
            'status'  =>  0
        );

        $this->db->where('user_id', $user_id);
        $this->db->where('post_id', $post_id);
        if($this->db->update('like', $data))
            return true;
        else
            return false;
    }

    public function post_like_count($post_id)
    {
        if(empty($post_id))
            return false;

        $this->db->where('status', 1);
        $this->db->where('post_id', $post_id);
        $this->db->select('id');
        $query = $this->db->get('like');

        if($query->num_rows())
        {
            return $query->num_rows();
        }
        else
            return false;
    }

    public function is_like($post_id, $user_id)
    {
        if(empty($post_id) || empty($user_id))
            return false;

        $this->db->where('status', 1);
        $this->db->where('user_id', $user_id);
        $this->db->where('post_id', $post_id);
        $this->db->select('id');
        $query = $this->db->get('like');

        if($query->num_rows())
        {
            return true;
        }
        else
            return false;
    }

    public function user_likes($user_id, $limit)
    {
        if(empty($user_id))
            return false;

        if(empty($limit) || !is_numeric($limit))
            $limit = 50;

        if($limit!=0)
            $this->db->limit($limit);
        $this->db->where('like.status', 1);
        $this->db->where('post.status', 1);
        $this->db->where('post.user_id', $user_id);
        $this->db->where('like.user_id!=', $user_id);
        $this->db->order_by('like.id', 'DESC');
        $this->db->join('post', 'post.id = like.post_id');
        $this->db->select('like.user_id, like.time');
        $this->db->from('like');
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