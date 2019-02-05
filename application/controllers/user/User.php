<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created : 07/11/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class User extends CI_Controller
{
	/* Private */
	private function parser($view_name, $my_data = null)
	{
		if(empty($view_name) || is_null($view_name))
		{
			show_404();
			return false;
		}

		$this->load->helper('security');
		$this->load->library('parser');

		$view_name = "ui/" . xss_clean($view_name);
		$data = array(
	        'base'	=>	$this->base_url()
		);
		if(!is_null($my_data))
			$data = array_merge($data, $my_data);

		$this->parser->parse($view_name, $data);
		return true;
	}

	private function self_set_url($url)
	{
		if(is_numeric(strpos($url, "/index.php")))
			show_404();
	}

	private function current_url()
	{
		return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}

	private function base_url()
	{
		$this->load->helper('url');
        return base_url();
	}

	private function is_login()
	{
		if(!$this->session->has_userdata('user_login') || $this->session->has_userdata('user_login')!==true || !$this->session->has_userdata('user_id'))
		{
			$this->load->helper('url');
			redirect($this->base_url() . "login");
			exit(0);
		}

		$this->load->model('user_model');
		if(!$this->user_model->user_enable($this->session->userdata('user_id')))
		{
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('user_login');
			$this->load->helper('url');
			redirect($this->base_url() . "login");
			exit(0);
		}

		/* Type 1 : Admin, Type 2 : User */
		if($this->user_model->get_type_by_id($this->session->userdata('user_id'))!=2)
		{
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('user_login');
			$this->load->helper('url');
			redirect($this->base_url() . "login");
			exit(0);
		}

		$current_method = $this->router->fetch_method();
		if($current_method!=="change_password" && $this->session->has_userdata('need_change_password') && $this->session->userdata('need_change_password')==true)
		{
			$this->load->helper('url');
			redirect($this->base_url() . "panel/change_password");
			exit(0);
		}
		elseif ($current_method==="change_password" && $this->session->has_userdata('need_change_password') && $this->session->userdata('need_change_password')==false) {
			$this->load->helper('url');
			redirect($this->base_url() . "panel");
			exit(0);
		}
	}

    private function time()
    {
        return time();
    }

    private function user_agent()
    {
        $this->load->library('user_agent');
        return $this->agent->agent_string();
    }

    private function user_ip()
    {
        return $this->input->ip_address();
    }

    private function character_limiter($string, $limit)
	{
		$result = substr($string, 0, $limit);
		if($result != $string)
			return $result . " ...";
		return $result;
	}

	private function word_limiter($str, $limit = 100, $end_char = '&#8230;')
	{
		if (trim($str) === '')
		{
			return $str;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

		if (strlen($str) === strlen($matches[0]))
		{
			$end_char = '';
		}

		return rtrim($matches[0]).$end_char;
	}

	private function array_sort($array, $on, $order = SORT_ASC)
	{
	    $new_array = array();
	    $sortable_array = array();
	    if (count($array) > 0)
	    {
	        foreach ($array as $k => $v)
	        {
	            if (is_array($v))
	            {
	                foreach ($v as $k2 => $v2)
	                {
	                    if ($k2 == $on)
	                    {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else
	            {
	                $sortable_array[$k] = $v;
	            }
	        }
	        switch ($order)
	        {
	            case SORT_ASC:
	                asort($sortable_array);
	                break;
	            case SORT_DESC:
	                arsort($sortable_array);
	                break;
	        }
	        foreach ($sortable_array as $k => $v)
	        {
	            $new_array[$k] = $array[$k];
	        }
	    }
	    return $new_array;
	}

	/* Public */
	public function index()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$form_newpost_open = form_open_multipart($this->base_url() . "user/form/newpost");
		$write_post_content = form_textarea(
			array(
				'id'			=>	'post_content',
				'name'			=>	'post_content',
				'maxlength'		=>	10000,
				'rows'			=> 	4,
				'placeholder'	=>	'چیزی بنویسید....',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$file_post_content = '<input type="file" id="file-upload" name="post_file" accept="image/*" />';
		$post_submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'اشتراک گذاری',
				'class'			=>	'btn bg-success text-light float-left'
			)
		);

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$this->load->model('post_model');
		$timeline_posts = $this->post_model->post_timeline($this->session->userdata('user_id'), 100);
		$timeline_posts_count = 1;

		$user_suggest_3 = $this->user_model->people_suggest(3, $this->session->userdata('user_id'));
		$user_suggest_5 = $this->user_model->people_suggest(5, $this->session->userdata('user_id'));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		if($this->session->has_userdata('post_delete')) {
			$post_delete = $this->session->userdata('post_delete');
			$this->session->unset_userdata('post_delete');
		}
		else {
			$post_delete="";
		}

		if($this->session->has_userdata('profile_success')) {
			$profile_success = $this->session->userdata('profile_success');
			$this->session->unset_userdata('profile_success');
		}
		else {
			$profile_success="";
		}

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_newpost_open'		=>	$form_newpost_open,
			'write_post_content'	=>	$write_post_content,
			'file_post_content'		=>	$file_post_content,
			'post_submit_input'		=>	$post_submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'timeline_posts'		=>	$timeline_posts,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'user_suggest_3'		=>	$user_suggest_3,
			'user_suggest_5'		=>	$user_suggest_5,
			'my_user_id'			=>	$this->session->userdata('user_id'),
			'profile_open_key'		=>	$profile_open_key,
			'post_delete'			=>	$post_delete,
			'profile_success'		=>	$profile_success,
			'timeline_posts_count'	=>	$timeline_posts_count+1
		);

		$this->parser('user/panel', $data);
	}

	public function index_more($timeline_posts_count = 2)
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$form_newpost_open = form_open_multipart($this->base_url() . "user/form/newpost");
		$write_post_content = form_textarea(
			array(
				'id'			=>	'post_content',
				'name'			=>	'post_content',
				'maxlength'		=>	10000,
				'rows'			=> 	4,
				'placeholder'	=>	'چیزی بنویسید....',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$file_post_content = '<input type="file" id="file-upload" name="post_file" accept="image/*" />';
		$post_submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'اشتراک گذاری',
				'class'			=>	'btn bg-success text-light float-left'
			)
		);

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		if(!is_numeric($timeline_posts_count) || $timeline_posts_count < 2)
			$timeline_posts_count = 2;
		$this->load->model('post_model');
		$timeline_posts = $this->post_model->post_timeline($this->session->userdata('user_id'), $timeline_posts_count * 100);

		$user_suggest_3 = $this->user_model->people_suggest(3, $this->session->userdata('user_id'));
		$user_suggest_5 = $this->user_model->people_suggest(5, $this->session->userdata('user_id'));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		if($this->session->has_userdata('post_delete')) {
			$post_delete = $this->session->userdata('post_delete');
			$this->session->unset_userdata('post_delete');
		}
		else {
			$post_delete="";
		}

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_newpost_open'		=>	$form_newpost_open,
			'write_post_content'	=>	$write_post_content,
			'file_post_content'		=>	$file_post_content,
			'post_submit_input'		=>	$post_submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'timeline_posts'		=>	$timeline_posts,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'user_suggest_3'		=>	$user_suggest_3,
			'user_suggest_5'		=>	$user_suggest_5,
			'my_user_id'			=>	$this->session->userdata('user_id'),
			'profile_open_key'		=>	$profile_open_key,
			'post_delete'			=>	$post_delete,
			'timeline_posts_count'	=>	$timeline_posts_count+1
		);

		$this->parser('user/panel', $data);
	}

	public function logout()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->session->unset_userdata('user_id');
		$this->session->unset_userdata('user_login');
		$this->load->helper('url');
		redirect($this->base_url() . "login");
		exit(0);
	}

	public function setting()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('user_option_model');
		$form_setting_open = form_open($this->base_url() . "user/form/setting");
		$user_private_page = $this->user_option_model->get_option($this->session->userdata('user_id'), 'private_page');
		$dropdown_1_options = array (
			'false'	=>	'بازدید توسط همگان',
			'true'	=>	'بازدید توسط دوستان'
		);
		$dropdown_1 = form_dropdown('private_page', $dropdown_1_options, $user_private_page, 'class="form-control"');
		
		$user_private_contact = $this->user_option_model->get_option($this->session->userdata('user_id'), 'private_contact');
		$dropdown_2_options = array (
			'false'	=>	'بازدید توسط همگان',
			'true'	=>	'بازدید توسط دوستان'
		);
		$dropdown_2 = form_dropdown('private_contact', $dropdown_2_options, $user_private_contact, 'class="form-control"');

		$user_private_avatar = $this->user_option_model->get_option($this->session->userdata('user_id'), 'private_avatar');
		$dropdown_3_options = array (
			'false'	=>	'بازدید توسط همگان',
			'true'	=>	'بازدید توسط دوستان'
		);
		$dropdown_3 = form_dropdown('private_avatar', $dropdown_3_options, $user_private_avatar, 'class="form-control"');

		$password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'password',
				'maxlength'		=>	40,
				'placeholder'	=>	'رمز عبور فعلی',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$new_password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'new_password',
				'maxlength'		=>	40,
				'placeholder'	=>	'رمز عبور تازه',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$new_repassword_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'new_repassword',
				'maxlength'		=>	40,
				'placeholder'	=>	'تکرار رمز عبور تازه',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت  تغییرات',
				'class'			=>	'btn bg-success text-light float-left'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_setting_open'		=>	$form_setting_open,
			'dropdown_1'			=>	$dropdown_1,
			'dropdown_2'			=>	$dropdown_2,
			'dropdown_3'			=>	$dropdown_3,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'password_input'		=>	$password_input,
			'new_password_input'	=>	$new_password_input,
			'new_repassword_input'	=>	$new_repassword_input,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/setting', $data);
	}

	public function profile()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));

		$this->load->model('user_item_model');
		$experience	= $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 1);
		$education	= $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 2);
		$skills		= $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 3);
		$project	= $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 4);

		$this->load->model('connections_model');
		$user_connection = $this->connections_model->user_connection($this->session->userdata('user_id'), 0);

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$this->load->model('country_model');
		$user_person 				= $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_person['country_id'] 	= $this->country_model->get_country_name($user_person['country_id']);

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter_value  = "";
		$linkedin_value = "";
		$telegram_value = "";
		$skype_value  	= "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin_value = $ucs['content'];
			if($ucs['type']==2)
				$twitter_value = $ucs['content'];
			if($ucs['type']==3)
				$telegram_value = $ucs['content'];
			if($ucs['type']==4)
				$skype_value = $ucs['content'];
		}

		$linkedin = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'linkedin',
				'maxlength'		=>	500,
				'placeholder'	=>	'linkedin',
				'class'			=>	'social-profile-input',
				'value'			=>	$linkedin_value
			)
		);
		$twitter = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'twitter',
				'maxlength'		=>	500,
				'placeholder'	=>	'twitter',
				'class'			=>	'social-profile-input',
				'value'			=>	$twitter_value
			)
		);
		$telegram = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'telegram',
				'maxlength'		=>	500,
				'placeholder'	=>	'telegram',
				'class'			=>	'social-profile-input',
				'value'			=>	$telegram_value
			)
		);
		$skype = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'skype',
				'maxlength'		=>	500,
				'placeholder'	=>	'skype',
				'class'			=>	'social-profile-input',
				'value'			=>	$skype_value
			)
		);

		$social_form_open   = form_open($this->base_url() . "user/form/editsocial");
		$social_submit 		= form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'✎',
				'class'			=>	'btn bg-success text-light float-right'
			)
		);

		if($this->session->has_userdata('social_success')) {
			$social_success = $this->session->userdata('social_success');
			$this->session->unset_userdata('social_success');
		}
		else {
			$social_success="";
		}

		if($this->session->has_userdata('social_error')) {
			$social_error = $this->session->userdata('social_error');
			$this->session->unset_userdata('social_error');
		}
		else {
			$social_error="";
		}
		
		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));


		$form_avatar_open = form_open_multipart($this->base_url() . "user/form/newavatar");
		$file_avatar_content = '<input type="file" id="file-upload" name="avatar_file" class="avatar-file" accept="image/png, image/jpeg, image/jpg" />';
		$avatar_submit 		= form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'✔',
				'class'			=>	'btn bg-success text-light d-inline'
			)
		);
		if($this->session->has_userdata('avatar_success')) {
			$avatar_success = $this->session->userdata('avatar_success');
			$this->session->unset_userdata('avatar_success');
		}
		else {
			$avatar_success="";
		}

		$user_id = $this->user_model->get_user_by_id($this->session->userdata('user_id'));
		$user_id = $user_id['id'];
		$profile_open_key = $this->base_url() . 'user/' . md5($user_id);
		
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$user_suggest_3 = $this->user_model->people_suggest(3, $this->session->userdata('user_id'));

		$this->load->model('connections_model');
		$summary_connection = $this->connections_model->user_connection($this->session->userdata('user_id'), 5);

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_person'			=>	$user_person,
			'experience'			=>	$experience,
			'education'				=>	$education,
			'skills'				=>	$skills,
			'project'				=>	$project,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_person'			=>	$user_person,
			'social_form_open'		=>	$social_form_open,
			'social_submit'			=>	$social_submit,
			'linkedin'				=>	$linkedin,
			'twitter'				=>	$twitter,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'social_success'		=>	$social_success,
			'social_error'			=>	$social_error,
			'user_connection_count'	=>  $user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'form_avatar_open'		=>	$form_avatar_open,
			'file_avatar_content'	=>	$file_avatar_content,
			'avatar_submit'			=>	$avatar_submit,
			'avatar_success'		=>	$avatar_success,
			'profile_open_key'		=>	$profile_open_key,
			'user_suggest_3'		=>	$user_suggest_3,
			'my_user_id'			=>	$this->session->userdata('user_id'),
			'user_full_name'		=>	$user_person['firstname'] . " " . $user_person['lastname'],
			'register_date'			=>	$register_date,
			'summary_connection'	=>	$summary_connection
		);

		$this->parser('user/profile', $data);
	}

	public function edit_person()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));

		$form_editperson_open = form_open($this->base_url() . "user/form/editperson");

		$firstname_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'firstname',
				'maxlength'		=>	100,
				'placeholder'	=>	'نام',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_person['firstname']
			)
		);
		$lastname_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'lastname',
				'maxlength'		=>	100,
				'placeholder'	=>	'نام خانوادگی',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_person['lastname']
			)
		);

		$this->load->model('country_model');
		$country = $this->country_model->select_all();
		foreach ($country as $mc) {
			$dropdown_1_options[$mc['id']] = $mc['name'];
		}
		$dropdown_1 = form_dropdown('country_id', $dropdown_1_options, $user_person['country_id'], 'class="form-control"');

		$zip_code_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'zip_code',
				'maxlength'		=>	20,
				'placeholder'	=>	'کدپستی',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_person['zip_code']
			)
		);
		$birthday_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'birthday',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ تولد',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_person['birthday']
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت  تغییرات',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_editperson_open'	=>	$form_editperson_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'firstname_input'		=>	$firstname_input,
			'lastname_input'		=>	$lastname_input,
			'dropdown_1'			=>	$dropdown_1,
			'zip_code_input'		=>	$zip_code_input,
			'birthday_input'		=>	$birthday_input,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/editperson', $data);
	}

	public function edit_bio()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('person_model');
		$user_bio = $this->person_model->read_user_biography($this->session->userdata('user_id'));
		$user_bio = $user_bio['biography'];

		$form_editbio_open = form_open($this->base_url() . "user/form/editbio");

		$bio_input = form_textarea(
			array(
				'name'			=>	'biography',
				'maxlength'		=>	255,
				'rows'			=> 	4,
				'placeholder'	=>	'بیوگرافی',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_bio
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت  تغییرات',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_editbio_open'		=>	$form_editbio_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'bio_input'				=>	$bio_input,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/editbio', $data);
	}

	public function edit_experience()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		if($this->session->has_userdata('database_action')) {
			$database_action = $this->session->userdata('database_action');
			$this->session->unset_userdata('database_action');
		}
		else {
			$database_action="";
		}

		$this->load->model('user_item_model');
		$user_experience = $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 1);

		$form_addexperience_open = form_open($this->base_url() . "user/form/add_experience");

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_addexperience_open'=>	$form_addexperience_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_experience'		=>	$user_experience,
			'database_action'		=>	$database_action,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/editexperience', $data);
	}

	public function single_experience($experience_id)
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		if(empty($experience_id) || !is_numeric($experience_id))
        {
            redirect($this->base_url() . "panel/profile/edit/experience");
            exit(0);
        }
        else
        {
            $this->load->helper('security');
            $experience_id = xss_clean(trim($experience_id));
        }

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('user_item_model');
		$user_single_experience = $this->user_item_model->read_single_special_type_item($this->session->userdata('user_id'), 1, $experience_id);

		if($user_single_experience===false)
		{
			redirect($this->base_url() . "panel/profile/edit/experience");
            exit(0);
		}

		$form_editexperience_open = form_open($this->base_url() . "user/form/edit_experience");

		$id_input = form_hidden('id', $user_single_experience['id']);

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_experience['title']
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_experience['content']
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_experience['start_date']
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_experience['end_date']
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_editexperience_open'=>$form_editexperience_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'id_input'				=>	$id_input,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_single_experience'=>	$user_single_experience,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/singleexperience', $data);
	}

	public function edit_education()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		if($this->session->has_userdata('database_action')) {
			$database_action = $this->session->userdata('database_action');
			$this->session->unset_userdata('database_action');
		}
		else {
			$database_action="";
		}

		$this->load->model('user_item_model');
		$user_education = $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 2);

		$form_addeducation_open = form_open($this->base_url() . "user/form/add_education");

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_addeducation_open'=>	$form_addeducation_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_education'		=>	$user_education,
			'database_action'		=>	$database_action,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/editeducation', $data);
	}

	public function single_education($education_id)
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		if(empty($education_id) || !is_numeric($education_id))
        {
            redirect($this->base_url() . "panel/profile/edit/education");
            exit(0);
        }
        else
        {
            $this->load->helper('security');
            $education_id = xss_clean(trim($education_id));
        }

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('user_item_model');
		$user_single_education = $this->user_item_model->read_single_special_type_item($this->session->userdata('user_id'), 2, $education_id);

		if($user_single_education===false)
		{
			redirect($this->base_url() . "panel/profile/edit/education");
            exit(0);
		}

		$form_editeducation_open = form_open($this->base_url() . "user/form/edit_education");

		$id_input = form_hidden('id', $user_single_education['id']);

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_education['title']
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_education['content']
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_education['start_date']
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_education['end_date']
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_editeducation_open'=>$form_editeducation_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'id_input'				=>	$id_input,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_single_education'=>	$user_single_education,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/singleeducation', $data);
	}

	public function edit_skills()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		if($this->session->has_userdata('database_action')) {
			$database_action = $this->session->userdata('database_action');
			$this->session->unset_userdata('database_action');
		}
		else {
			$database_action="";
		}

		$this->load->model('user_item_model');
		$user_skills = $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 3);

		$form_addskills_open = form_open($this->base_url() . "user/form/add_skills");

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_addskills_open'	=>	$form_addskills_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_skills'			=>	$user_skills,
			'database_action'		=>	$database_action,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/editskills', $data);
	}

	public function single_skills($skills_id)
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		if(empty($skills_id) || !is_numeric($skills_id))
        {
            redirect($this->base_url() . "panel/profile/edit/skills");
            exit(0);
        }
        else
        {
            $this->load->helper('security');
            $skills_id = xss_clean(trim($skills_id));
        }

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('user_item_model');
		$user_single_skills = $this->user_item_model->read_single_special_type_item($this->session->userdata('user_id'), 3, $skills_id);

		if($user_single_skills===false)
		{
			redirect($this->base_url() . "panel/profile/edit/skills");
            exit(0);
		}

		$form_editskills_open = form_open($this->base_url() . "user/form/edit_skills");

		$id_input = form_hidden('id', $user_single_skills['id']);

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_skills['title']
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_skills['content']
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_skills['start_date']
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_skills['end_date']
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_editskills_open'	=>$form_editskills_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'id_input'				=>	$id_input,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_single_skills'	=>	$user_single_skills,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/singleskills', $data);
	}

	public function edit_project()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		if($this->session->has_userdata('database_action')) {
			$database_action = $this->session->userdata('database_action');
			$this->session->unset_userdata('database_action');
		}
		else {
			$database_action="";
		}

		$this->load->model('user_item_model');
		$user_project = $this->user_item_model->read_user_special_type_item($this->session->userdata('user_id'), 4);

		$form_addproject_open = form_open($this->base_url() . "user/form/add_project");

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_addproject_open'	=>	$form_addproject_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_project'			=>	$user_project,
			'database_action'		=>	$database_action,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/editproject', $data);
	}

	public function single_project($project_id)
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		if(empty($project_id) || !is_numeric($project_id))
        {
            redirect($this->base_url() . "panel/profile/edit/project");
            exit(0);
        }
        else
        {
            $this->load->helper('security');
            $project_id = xss_clean(trim($project_id));
        }

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('user_item_model');
		$user_single_project = $this->user_item_model->read_single_special_type_item($this->session->userdata('user_id'), 4, $project_id);

		if($user_single_project===false)
		{
			redirect($this->base_url() . "panel/profile/edit/project");
            exit(0);
		}

		$form_editproject_open = form_open($this->base_url() . "user/form/edit_project");

		$id_input = form_hidden('id', $user_single_project['id']);

		$title_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'title',
				'maxlength'		=>	100,
				'placeholder'	=>	'عنوان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_project['title']
			)
		);

		$content_input = form_textarea(
			array(
				'name'			=>	'content',
				'maxlength'		=>	1000,
				'rows'			=> 	4,
				'placeholder'	=>	'توضیحات',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_project['content']
			)
		);

		$start_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'start_date',
				'maxlength'		=>	10,
				'placeholder'	=>	'تاریخ شروع',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_project['start_date']
			)
		);

		$end_date_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'end_date',
				'maxlength'		=>	100,
				'placeholder'	=>	'تاریخ پایان',
				'class'			=>	'form-control text-right right-to-left',
				'value'			=>	$user_single_project['end_date']
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت',
				'class'			=>	'btn bg-success text-light'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_editproject_open'	=>	$form_editproject_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'id_input'				=>	$id_input,
			'title_input'			=>	$title_input,
			'content_input'			=>	$content_input,
			'start_date_input'		=>	$start_date_input,
			'end_date_input'		=>	$end_date_input,
			'user_single_project'	=>	$user_single_project,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/singleproject', $data);
	}

	public function notification()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$this->load->model('like_model');
		$likes = $this->like_model->user_likes($this->session->userdata('user_id'), 20);
		if($likes!==false)
		foreach ($likes as &$my_likes) {
			$my_likes['type']=1;
		}

		$this->load->model('profile_view_model');
		$views = $this->profile_view_model->user_views($this->session->userdata('user_id'), 20);
		if($views!==false)
		foreach ($views as &$my_views) {
			$my_views['type']=2;
		}

		if($likes!==false && $views!==false)
			$notification = array_merge($likes, $views);
		elseif ($likes!==false && $views===false) {
			$notification = $likes;
		}
		elseif ($likes===false && $views!==false) {
			$notification = $views;
		}
		else
			$notification = false;

		if($notification!==false)
			$notification = $this->array_sort($notification, 'time', SORT_DESC);


		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key,
			'notification'			=>	$notification
		);

		$this->parser('user/notification', $data);
	}

	public function message()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$this->load->model('message_model');
		$message = $this->message_model->active_chat_user($this->session->userdata('user_id'));

		if($this->session->has_userdata('noconnection_message')) {
			$noconnection_message = $this->session->userdata('noconnection_message');
			$this->session->unset_userdata('noconnection_message');
		}
		else {
			$noconnection_message=0;
		}

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key,
			'message'				=>	$message,
			'noconnection_message'	=>	$noconnection_message,
			'my_id'					=>	$this->session->userdata('user_id')
		);

		$this->parser('user/message', $data);
	}

	public function new_message()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		if($user_connection_count===false || $user_connection_count===0)
		{
			$this->session->set_userdata('noconnection_message', 1);
			redirect($this->base_url() . "panel/message");
		}

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$this->load->model('connections_model');
		$connections = $this->connections_model->user_connection($this->session->userdata('user_id'));

		$form_newmessage_open = form_open($this->base_url() . "user/form/newmessage");
		$dropdown_1_options[0] = 'کاربری را برای شروع مکالمه انتخاب کنید.';
		foreach ($connections as $my_connections) {
			$dropdown_1_options[$my_connections['connected_id']] = $my_connections['firstname'] . " " . $my_connections['lastname'];
		}
		$dropdown_1 = form_dropdown('newmessage', $dropdown_1_options, 1, 'class="form-control"');
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'شروع مکالمه',
				'class'			=>	'btn bg-primary text-light float-left'
			)
		);

		if($this->session->has_userdata('new_message_error')) {
			$new_message_error = $this->session->userdata('new_message_error');
			$this->session->unset_userdata('new_message_error');
		}
		else {
			$new_message_error=0;
		}

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key,
			'form_newmessage_open'	=>	$form_newmessage_open,
			'dropdown_1'			=>	$dropdown_1,
			'submit_input'			=>	$submit_input,
			'new_message_error'		=>	$new_message_error
		);

		$this->parser('user/newmessage', $data);
	}

	public function change_password()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		$this->load->model('user_option_model');
		$form_setting_open = form_open($this->base_url() . "user/form/change_password");

		$password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'password',
				'maxlength'		=>	40,
				'placeholder'	=>	'رمز عبور فعلی',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$new_password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'new_password',
				'maxlength'		=>	40,
				'placeholder'	=>	'رمز عبور تازه',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$new_repassword_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'new_repassword',
				'maxlength'		=>	40,
				'placeholder'	=>	'تکرار رمز عبور تازه',
				'class'			=>	'form-control text-right right-to-left'
			)
		);

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت  تغییرات',
				'class'			=>	'btn bg-success text-light float-left'
			)
		);

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_setting_open'		=>	$form_setting_open,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'password_input'		=>	$password_input,
			'new_password_input'	=>	$new_password_input,
			'new_repassword_input'	=>	$new_repassword_input,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/changepassword', $data);
	}

	public function edit_post($post_key)
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		if(empty($post_key))
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->helper('security');
        $post_key = trim(xss_clean($post_key));
        $this->load->model('post_model');
        $post = $this->post_model->find_post($post_key);
        if($post===false)
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        if($this->session->userdata('user_id')!=$post['user_id'])
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$form_editpost_open = form_open_multipart($this->base_url() . "user/form/edit_post/" . $post_key);
		$edit_post_content = form_textarea(
			array(
				'id'			=>	'post_content',
				'name'			=>	'post_content',
				'maxlength'		=>	10000,
				'rows'			=> 	strlen($post['content']) / 130,
				'value'			=>	$post['content'],
				'placeholder'	=>	'متن نوشته ی خود را بنویسید...',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ویرایش نوشته',
				'class'			=>	'btn bg-success text-light float-left'
			)
		);

		if($this->session->has_userdata('form_error')) {
			$validation_errors = $this->session->userdata('form_error');
			$this->session->unset_userdata('form_error');
		}
		else {
			$validation_errors="";
		}

		if($this->session->has_userdata('form_success')) {
			$form_success = $this->session->userdata('form_success');
			$this->session->unset_userdata('form_success');
		}
		else {
			$form_success="";
		}

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$user_suggest_3 = $this->user_model->people_suggest(3, $this->session->userdata('user_id'));
		$user_suggest_5 = $this->user_model->people_suggest(5, $this->session->userdata('user_id'));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'form_editpost_open'	=>	$form_editpost_open,
			'edit_post_content'		=>	$edit_post_content,
			'submit_input'			=>	$submit_input,
			'validation_errors'		=>	$validation_errors,
			'form_success'			=>	$form_success,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'user_suggest_3'		=>	$user_suggest_3,
			'user_suggest_5'		=>	$user_suggest_5,
			'my_user_id'			=>	$this->session->userdata('user_id'),
			'profile_open_key'		=>	$profile_open_key,
			'post'					=>	$post
		);

		$this->parser('user/editpost', $data);
	}

	public function connections()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$this->load->model('connections_model');
		$connections = $this->connections_model->user_connection($this->session->userdata('user_id'));
		$respond_connections = $this->connections_model->user_respondconnection($this->session->userdata('user_id'));

		if($this->session->has_userdata('profile_success')) {
			$profile_success = $this->session->userdata('profile_success');
			$this->session->unset_userdata('profile_success');
		}
		else {
			$profile_success="";
		}

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key,
			'connections'			=>	$connections,
			'respond_connections'	=>	$respond_connections,
			'profile_success'		=>	$profile_success
		);

		$this->parser('user/connections', $data);
	}

	public function rules()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close 	= form_close();

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key
		);

		$this->parser('user/rules', $data);
	}

	public function chat($user_key)
	{
		$this->self_set_url($this->current_url());
		$this->is_login();

		$this->load->helper('url');
		if(empty($user_key) || is_null($user_key))
        {
            redirect($this->base_url() . "panel/message");
            exit(0);
        }

        $this->load->helper('security');
        $user_key = trim(xss_clean($user_key));
        $this->load->model('user_model');
        $user = $this->user_model->find_profile($user_key);
        if($user===false)
        {
            redirect($this->base_url() . "panel/message");
            exit(0);
        }

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "panel/message");
            exit(0);
        }

        $this->load->model('block_model');
        if($this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        { 
            redirect($this->base_url() . "panel/message");
            exit(0);
        }
       	$this->load->model('connections_model');
        if(!$this->connections_model->is_connection($this->session->userdata('user_id'), $user['id']) || !$this->connections_model->is_connection($this->session->userdata('user_id'), $user['id']))
        {
            redirect($this->base_url() . "panel/message");
            exit(0);
        }

		$this->load->helper('form');
		$form_search_open = form_open($this->base_url() . "user/form/search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$form_close = form_close();

		$this->load->model('connections_model');
		$user_connection_count = $this->connections_model->user_connection_count($this->session->userdata('user_id'));
		if($user_connection_count===false)
			$user_connection_count = 0;

		$this->load->model('profile_view_model');
		$user_view_profile = $this->profile_view_model->viewed_profile_count($this->session->userdata('user_id'));
		if($user_view_profile===false)
			$user_view_profile = 0;

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$reciver_person = $this->person_model->read_user_person($user['id']);
		$reciver_full_name = $reciver_person['firstname'] . " " . $reciver_person['lastname'];

		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($this->session->userdata('user_id'));
		$twitter  = "";
		$linkedin = "";
		$telegram = "";
		$skype    = "";
		foreach ($user_contact as $ucs) {
			if($ucs['type']==1)
				$linkedin = $ucs['content'];
			if($ucs['type']==2)
				$twitter = $ucs['content'];
			if($ucs['type']==3)
				$telegram = $ucs['content'];
			if($ucs['type']==4)
				$skype = $ucs['content'];
		}

		$this->load->model('user_model');
		$this->load->library('jdf');
		$register_date = $this->jdf->jdate('d / m / Y', $this->user_model->get_register_time_id($this->session->userdata('user_id')));

		$profile_open_key = $this->base_url() . 'user/' . md5($this->session->userdata('user_id'));

		$form_chat_open = form_open($this->base_url() . "user/form/send_message", 'id="ajaxform"');
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'☑',
				'class'			=>	'btn bg-dark text-light refresh_key',
				'id'			=>	'refresh_key'
			)
		);
		$textbox = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'textbox',
				'id'			=>	'textbox',
				'maxlength'		=>	255,
				'placeholder'	=>	'تایپ + اینتر',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$reciver_message = form_hidden('reciver_message', $user['id']);

		$this->load->model('message_model');
		$chat = $this->message_model->load_chat($this->session->userdata('user_id'), $user['id']);

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_connection_count'	=>	$user_connection_count,
			'user_view_profile'		=>	$user_view_profile,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'twitter_limit'			=>	$this->character_limiter($twitter, 30),
			'linkedin_limit'		=>	$this->character_limiter($linkedin, 30),
			'telegram_limit'		=>	$this->character_limiter($telegram, 30),
			'skype_limit'			=>	$this->character_limiter($skype, 30),
			'twitter'				=>	$twitter,
			'linkedin'				=>	$linkedin,
			'telegram'				=>	$telegram,
			'skype'					=>	$skype,
			'register_date'			=>	$register_date,
			'profile_open_key'		=>	$profile_open_key,
			'form_chat_open'		=>	$form_chat_open,
			'submit_input'			=>	$submit_input,
			'textbox'				=>	$textbox,
			'chat'					=>	$chat,
			'reciver_message'		=>	$reciver_message,
			'reciver_full_name'		=>	$reciver_full_name,
			'reciver_message_id'	=>	$user['id'],
			'my_user_id'			=>	$this->session->userdata('user_id')

		);

		$this->parser('user/chat', $data);
	}

}
