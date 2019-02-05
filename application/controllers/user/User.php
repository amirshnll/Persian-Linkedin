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

		$this->load->model('avatar_model');
		$user_current_avatar = $this->avatar_model->user_current_avatar($this->session->userdata('user_id'));

		$this->load->model('person_model');
		$user_person = $this->person_model->read_user_person($this->session->userdata('user_id'));
		$user_full_name = $user_person['firstname'] . " " . $user_person['lastname'];

		$form_newpost_open = form_open($this->base_url() . "user/form/newpost");
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

		$data = array(
			'form_search_open'		=>	$form_search_open,
			'search_input'			=>	$search_input,
			'form_close'			=>	$form_close,
			'user_current_avatar'	=>	$user_current_avatar,
			'user_full_name'		=>	$user_full_name,
			'form_newpost_open'		=>	$form_newpost_open,
			'write_post_content'	=>	$write_post_content
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

		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت  تغییرات',
				'class'			=>	'btn bg-success text-light float-left'
			)
		);

		$data = array(
			'form_search_open'	=>	$form_search_open,
			'search_input'		=>	$search_input,
			'form_close'		=>	$form_close,
			'form_setting_open'	=>	$form_setting_open,
			'dropdown_1'		=>	$dropdown_1,
			'dropdown_2'		=>	$dropdown_2,
			'dropdown_3'		=>	$dropdown_3,
			'submit_input'		=>	$submit_input,
			'validation_errors'	=>	$validation_errors,
			'form_success'		=>	$form_success
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
		$data = array(
			'form_search_open'	=>	$form_search_open,
			'search_input'		=>	$search_input,
			'form_close'		=>	$form_close
		);

		$this->parser('user/profile', $data);
	}

	public function notification()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();
	}

	public function message()
	{
		$this->self_set_url($this->current_url());
		$this->is_login();
	}

}
