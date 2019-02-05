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
		if(is_numeric(strpos($url, "panel")))
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


	/* Public */
	public function index()
	{
		$this->self_set_url($this->current_url());

		$this->load->helper('form');
		$form_open = form_open($this->base_url() . "user/form/login");
		$email_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'email',
				'maxlength'		=>	100,
				'placeholder'	=>	'ایمیل',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'password',
				'maxlength'		=>	40,
				'placeholder'	=>	'رمز عبور',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ورود',
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
			'alphabet'		=>	array("ا", "ب", "پ", "ت", "ث", "ج", "چ", "ح", "خ", "د", "ذ", "ر", "ز", "ژ", "س", "ش", "ص", "ض", "ط", "ظ", "ع", "غ", "ف", "ق", "ک", "گ", "ل", "م", "ن", "و", "هـ", "ی")
		);

		$this->parser('user/login', $data);
	}

	public function forget()
	{
		$this->self_set_url($this->current_url());

		$this->load->helper('form');
		$form_open = form_open($this->base_url() . "user/form/forget");
		$email_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'email',
				'maxlength'		=>	100,
				'placeholder'	=>	'ایمیل',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'بازیابی',
				'class'			=>	'btn bg-light'
			)
		);
		$form_close 	= form_close();

		$data = array(
			'form_open' 	=>	$form_open,
			'email_input'	=>	$email_input,
			'submit_input'	=>	$submit_input,
			'form_close'	=>	$form_close,
			'alphabet'		=>	array("ا", "ب", "پ", "ت", "ث", "ج", "چ", "ح", "خ", "د", "ذ", "ر", "ز", "ژ", "س", "ش", "ص", "ض", "ط", "ظ", "ع", "غ", "ف", "ق", "ک", "گ", "ل", "م", "ن", "و", "هـ", "ی")
		);

		$this->parser('user/forget', $data);
	}

	public function register()
	{
		$this->self_set_url($this->current_url());

		$this->load->helper('form');
		$form_open = form_open($this->base_url() . "user/form/register");
		$firstname_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'firstname',
				'maxlength'		=>	100,
				'placeholder'	=>	'نام',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$lastname_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'lastname',
				'maxlength'		=>	100,
				'placeholder'	=>	'نام خانوادگی',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$email_input = form_input(
			array(
				'type'			=>	'text',
				'name'			=>	'email',
				'maxlength'		=>	100,
				'placeholder'	=>	'ایمیل',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$password_input = form_input(
			array(
				'type'			=>	'password',
				'name'			=>	'password',
				'maxlength'		=>	40,
				'placeholder'	=>	'رمز عبور',
				'class'			=>	'form-control text-right right-to-left'
			)
		);
		$submit_input = form_input(
			array(
				'type'			=>	'submit',
				'name'			=>	'submit',
				'value'			=>	'ثبت نام',
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
			'alphabet'			=>	array("ا", "ب", "پ", "ت", "ث", "ج", "چ", "ح", "خ", "د", "ذ", "ر", "ز", "ژ", "س", "ش", "ص", "ض", "ط", "ظ", "ع", "غ", "ف", "ق", "ک", "گ", "ل", "م", "ن", "و", "هـ", "ی")
		);

		$this->parser('user/register', $data);
	}

}
