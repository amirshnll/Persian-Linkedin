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
        $this->db->group_by('post.id', 'DESC');
        $this->db->or_where('post.user_id', $user_id);
        $this->db->where('post.status', 1);
        $this->db->where('avatar.status', 1);
        $this->db->where('user.status', 1);
        $this->db->or_where('connections.status', 1);
        $this->db->or_where('connections.connected_id', $user_id);
        $this->db->select('file.filename, post.file_id, post.content, post.create_time, post.updated_time, post.status, post.user_agent, person.firstname, person.lastname, avatar.filename AS avatar_file_name, user.id AS user_post_id');
        $this->db->from('post');
        $this->db->join('user', 'user.id = post.user_id');
        $this->db->join('connections', 'connections.connected_id = post.user_id');
        $this->db->join('avatar', 'avatar.user_id = post.user_id');
        $this->db->join('file', 'file.id = post.file_id');
        $this->db->join('person', 'person.user_id = post.user_id');
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