<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created : 26/10/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Web extends CI_Controller
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
	
	private function check_login()
	{
		if(!$this->session->has_userdata('user_login') || $this->session->has_userdata('user_login')!==true || !$this->session->has_userdata('user_id'))
    	   return false;
        else
            return true;
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
		if($this->check_login())
		{
			$this->load->helper('url');
			redirect($this->base_url() . "panel");
			exit(0);
		}

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

		$this->load->helper('form');
		$form_open = form_open($this->base_url() . "user/form/login");
		$email_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'email',
				'maxlength'		=>	100,
				'placeholder'	=>	'⛶ ایمیل',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'password',
				'maxlength'		=>	40,
				'placeholder'	=>	'⛶ رمز عبور',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ورود  ⚐',
				'class'			=>	'btn bg-light'
			)
		);
		$form_close 	= form_close();

		$data = array(
			'form_open' 	=>	$form_open,
			'email_input'	=>	$email_input,
			'password_input'=>	$password_input,
			'submit_input'	=>	$submit_input,
			'form_close'	=>	$form_close,
			'alphabet'		=>	array("ا", "ب", "پ", "ت", "ث", "ج", "چ", "ح", "خ", "د", "ذ", "ر", "ز", "ژ", "س", "ش", "ص", "ض", "ط", "ظ", "ع", "غ", "ف", "ق", "ک", "گ", "ل", "م", "ن", "و", "هـ", "ی"),
			'validation_errors'	=>	$validation_errors,
			'form_success'		=>	$form_success
		);

		$this->parser('user/login', $data);
	}

	public function forget()
	{
		$this->self_set_url($this->current_url());
		if($this->check_login())
		{
			$this->load->helper('url');
			redirect($this->base_url() . "panel");
			exit(0);
		}

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

		$this->load->helper('form');
		$form_open = form_open($this->base_url() . "user/form/forget");
		$email_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'email',
				'maxlength'		=>	100,
				'placeholder'	=>	'⛶ ایمیل',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'بازیابی ⚐',
				'class'			=>	'btn bg-light'
			)
		);
		$form_close 	= form_close();

		$data = array(
			'form_open' 	=>	$form_open,
			'email_input'	=>	$email_input,
			'submit_input'	=>	$submit_input,
			'form_close'	=>	$form_close,
			'alphabet'		=>	array("ا", "ب", "پ", "ت", "ث", "ج", "چ", "ح", "خ", "د", "ذ", "ر", "ز", "ژ", "س", "ش", "ص", "ض", "ط", "ظ", "ع", "غ", "ف", "ق", "ک", "گ", "ل", "م", "ن", "و", "هـ", "ی"),
			'validation_errors'	=>	$validation_errors,
			'form_success'		=>	$form_success
		);

		$this->parser('user/forget', $data);
	}

	public function register()
	{
		$this->self_set_url($this->current_url());
		if($this->check_login())
		{
			$this->load->helper('url');
			redirect($this->base_url() . "panel");
			exit(0);
		}

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

		$this->load->helper('form');
		$form_open = form_open($this->base_url() . "user/form/register");
		$firstname_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'firstname',
				'maxlength'		=>	100,
				'placeholder'	=>	'⛶ نام',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$lastname_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'lastname',
				'maxlength'		=>	100,
				'placeholder'	=>	'⛶ نام خانوادگی',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$email_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'email',
				'maxlength'		=>	100,
				'placeholder'	=>	'⛶ ایمیل',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'password',
				'maxlength'		=>	40,
				'placeholder'	=>	'⛶ رمز عبور',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت نام ⚐',
				'class'			=>	'btn bg-light'
			)
		);
		$form_close 	= form_close();

		$data = array(
			'form_open' 		=>	$form_open,
			'firstname_input'	=>	$firstname_input,
			'lastname_input'	=>	$lastname_input,
			'email_input'		=>	$email_input,
			'password_input'	=>	$password_input,
			'submit_input'		=>	$submit_input,
			'form_close'		=>	$form_close,
			'alphabet'			=>	array("ا", "ب", "پ", "ت", "ث", "ج", "چ", "ح", "خ", "د", "ذ", "ر", "ز", "ژ", "س", "ش", "ص", "ض", "ط", "ظ", "ع", "غ", "ف", "ق", "ک", "گ", "ل", "م", "ن", "و", "هـ", "ی"),
			'validation_errors'	=>	$validation_errors,
			'form_success'		=>	$form_success
		);

		$this->parser('user/register', $data);
	}

	public function profile($user_key)
	{
		$this->self_set_url($this->current_url());

		if(empty($user_key) || is_null($user_key))
		{
			show_404();
			exit(0);
		}

		/* Load User Page */
		$this->load->helper('security');
		$user_key = trim(xss_clean($user_key));
		$this->load->model('user_model');
		$user = $this->user_model->find_profile($user_key);
		if($user===false)
		{
			show_404();
			exit(0);
		}

		/* User Privacy */
		$this->load->model('user_option_model');
		$user_private_page = $this->user_option_model->get_option($user['id'], 'private_page');
		$user_private_contact = $this->user_option_model->get_option($user['id'], 'private_contact');
		$user_private_avatar = $this->user_option_model->get_option($user['id'], 'private_avatar');

		/* Check System */
		if($this->check_login())
		{
			$user_id = $this->session->userdata('user_id');

			$this->load->model('block_model');
			if($this->block_model->is_block($user['id'], $user_id))
			{
				show_404();
				exit(0);
			}
		}
		else
		{
			if($user_private_page == "true")
			{
				show_404();
				exit(0);
			}
			$user_id = null;
		}

		/* View Page With Friend */
		if(!is_null($user_id) && $user_private_page=="true" && $user['id'] != $user_id)
		{
			$this->load->model('connections_model');
			if(!$this->connections_model->is_connection($user['id'], $user_id))
			{
				show_404();
				exit(0);
			}
		}

		/* Load Person User Data */
		$this->load->model('person_model');
		$person = $this->person_model->read_user_person($user['id']);
		$this->load->model('country_model');
		$person['country_id'] = $this->country_model->get_country_name($person['country_id']);

		/* Load Avatar User Data */
		$this->load->model('avatar_model');
		$avatar = $this->avatar_model->user_current_avatar($user['id']);
		if($user_private_avatar=="true" && $user['id'] != $user_id) {
			$this->load->model('connections_model');
			if(!$this->connections_model->is_connection($user['id'], $user_id))
			{
				$avatar = "default.png";
				$user_private_contact = "true";
			}
		}

		/* Load Items User Data */
		$this->load->model('user_item_model');
		$experience	= $this->user_item_model->read_user_special_type_item($user['id'], 1);
		$education	= $this->user_item_model->read_user_special_type_item($user['id'], 2);
		$skills		= $this->user_item_model->read_user_special_type_item($user['id'], 3);
		$project	= $this->user_item_model->read_user_special_type_item($user['id'], 4);

		/* Load Contact User Data */
		$this->load->model('contact_model');
		$user_contact = $this->contact_model->user_all_contact($user['id']);
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

		$is_login = $this->check_login();
		$profile_open_key = "";
		if($is_login == true)
		{
			$user_id = $this->user_model->get_user_by_id($this->session->userdata('user_id'));
			$user_id = $user_id['id'];
			$profile_open_key = $this->base_url() . 'user/' . md5($user_id);
		}

		$is_my_page = false;
		if($is_login==true && $user['id'] == $user_id)
			$is_my_page = true;

		$this->load->model('connections_model');
		$is_friend = false;
		if($is_login==true && $this->connections_model->is_connection($user['id'], $user_id))
			$is_friend = true;

		$is_respond = false;
		if($this->connections_model->is_respond_connection($user['id'], $user_id))
			$is_respond = true;

		$is_requester = false;
		if($this->connections_model->is_requester_connection($user_id, $user['id']))
			$is_requester = true;

		$this->load->model('block_model');
		$is_block = false;
		if($is_login==true && $this->block_model->is_block($user_id, $user['id']))
			$is_block = true;

		/* ViewUp User Profile View */
		$this->load->model('profile_view_model');
		$this->profile_view_model->insert($user_id, $user['id'], $this->time());

		/* Profile Alert */
		if($this->session->has_userdata('profile_error')) {
			$profile_error = $this->session->userdata('profile_error');
			$this->session->unset_userdata('profile_error');
		}
		else {
			$profile_error="";
		}
		if($this->session->has_userdata('profile_success')) {
			$profile_success = $this->session->userdata('profile_success');
			$this->session->unset_userdata('profile_success');
		}
		else {
			$profile_success="";
		}

		$data = array(
			'user_key'				=>	$user_key,
			'email'					=>	$user['email'],
			'register_time'			=>	$user['register_time'],
			'person'				=>	$person,
			'avatar'				=>	$avatar,
			'experience'			=>	$experience,
			'education'				=>	$education,
			'skills'				=>	$skills,
			'project'				=>	$project,
			'twitter'				=>	$twitter_value,
			'linkedin'				=>	$linkedin_value,
			'telegram'				=>	$telegram_value,
			'skype'					=>	$skype_value,
			'user_private_contact'	=>	$user_private_contact,
			'is_login'				=>	$is_login,
			'profile_open_key'		=>	$profile_open_key,
			'is_my_page'			=>	$is_my_page,
			'is_friend'				=>	$is_friend,
			'is_block'				=>	$is_block,
			'profile_success'		=>	$profile_success,
			'profile_error'			=>	$profile_error,
			'is_respond'			=>	$is_respond,
			'is_requester'			=>	$is_requester
		);

		$this->parser('web/profile', $data);
	}

	public function rules()
	{
		$this->self_set_url($this->current_url());
		if($this->check_login())
		{
			$this->load->helper('url');
			redirect($this->base_url() . "panel");
			exit(0);
		}

		$data = array(
			
		);

		$this->parser('web/rules', $data);
	}

	public function find()
	{
		$this->self_set_url($this->current_url());
		if($this->check_login())
		{
			$this->load->helper('url');
			redirect($this->base_url() . "panel");
			exit(0);
		}

		$this->load->helper('form');
		$form_open = form_open($this->base_url() . "user/form/out_search");
		$search_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'search',
				'maxlength'		=>	255,
				'placeholder'	=>	'⛶ جستجو',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'جستجو  ⚐',
				'class'			=>	'btn bg-light'
			)
		);
		$form_close 	= form_close();

		$data = array(
			'form_open' 	=>	$form_open,
			'search_input'	=>	$search_input,
			'submit_input'	=>	$submit_input,
			'form_close'	=>	$form_close,
			'alphabet'		=>	array("ا", "ب", "پ", "ت", "ث", "ج", "چ", "ح", "خ", "د", "ذ", "ر", "ز", "ژ", "س", "ش", "ص", "ض", "ط", "ظ", "ع", "غ", "ف", "ق", "ک", "گ", "ل", "م", "ن", "و", "هـ", "ی")
		);

		$this->parser('web/find', $data);
	}

}
