<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created : 26/10/2018
 * Author : A.shokri
 * Mail : amirsh.nll@gmail.com
 
 */

class Form extends CI_Controller
{
	/* Private */
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
		show_404();
	}

	public function login()
	{
		$this->self_set_url($this->current_url());
		echo 'ok';
	}

	public function forget()
	{
		$this->self_set_url($this->current_url());
		echo 'ok';
	}

	public function register()
	{
		$this->self_set_url($this->current_url());
		echo 'ok';
	}

}