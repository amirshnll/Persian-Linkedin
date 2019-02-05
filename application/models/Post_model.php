<?php

/**
 * Created : 01/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Post_model extends CI_Model {

    public function __construct()
    {
    	parent::__construct();
    	$this->load->database();
    }
    
    public function insert($user_id, $file_id, $content, $create_time, $updated_time, $status, $user_agent)
    {
    	if(empty($user_id) || empty($content) || empty($status) || empty($user_agent))
    		return false;

        if(empty($create_time))
            $create_time = time();

        if(empty($updated_time))
            $updated_time = time();

    	$data = array(
    		'user_id'		=>	$user_id,
			'file_id'		=>	$file_id,
			'content'		=>	$content,
			'create_time'	=>	$create_time,
			'updated_time'	=>	$updated_time,
			'status'		=>	$status,
			'user_agent'	=>	$user_agent
    	);
    	
    	if($this->db->insert('post', $data))
    		return true;
    	else
    		return false;
    }

    public function post_timeline($user_id, $limit)
    {
        if(empty($limit) || !is_numeric($limit))
            $limit = 100;

        if($limit!=0)
            $this->db->limit($limit);
        $this->db->order_by('post.updated_time', 'DESC');
        $this->db->where('post.status', 1);
        $this->db->where('user.status', 1);
        $this->db->select('post.id, post.user_id, post.file_id, post.content, post.create_time, post.updated_time, post.status, post.user_agent');
        $this->db->from('post');
        $this->db->join('user', 'user.id = post.user_id', 'left');
        $this->db->group_by('post.id');
        $query = $this->db->get('');

        if($query->num_rows())
        {
            $query = $query->result_array();
            return $query;
        }
        else
            return false;
    }

    public function find_post($string_find)
    {
        if(empty($string_find))
            return false;

        $this->db->where('status', 1);
        $query = $this->db->get('post');

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

    public function delete($id, $user_id)
    {
        if(empty($id) || empty($user_id))
            return false;

        $data = array(
            'status'  =>  0
        );

        $this->db->where('user_id', $user_id);
        $this->db->where('id', $id);
        if($this->db->update('post', $data))
            return true;
        else
            return false;
    }

    public function update($id, $user_id, $content, $updated_time, $user_agent)
    {
        if(empty($id) || empty($user_id) || empty($content) || empty($user_agent))
            return false;

        if(empty($updated_time))
            $updated_time = time();

        $data = array(
            'content'       =>  $content,
            'updated_time'  =>  $updated_time,
            'user_agent'    =>  $user_agent
        );
        
        $this->db->where('status', 1);
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $id);
        if($this->db->update('post', $data))
            return true;
        else
            return false;
    }

}

?>