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
		$form_open = form_open($this->base_url() . "user/form/search");
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
			'form_open'		=>	$form_open,
			'search_input'	=>	$search_input,
			'form_close'	=>	$form_close
		);

		$this->parser('user/panel', $data);
	}

	public function logout()
	{
		$this->session->unset_userdata('user_id');
		$this->session->unset_userdata('user_login');
		$this->load->helper('url');
		redirect($this->base_url() . "login");
		exit(0);
	}

}
