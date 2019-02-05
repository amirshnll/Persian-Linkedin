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

	/* Public */
	public function index()
	{
		show_404();
	}

	public function login()
	{
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if($this->check_login())
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();

        $rules = array(
            array(
                'field' =>  'email',
                'label' =>  'ایمیل',
                'rules' =>  'required|valid_email|min_length[5]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'valid_email'=> 'لطفا یک %s معتبر وارد کنید',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'password',
                'label' =>  'رمزعبور',
                'rules' =>  'required|min_length[5]|max_length[40]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "login");
            exit(0);
        }
        else
        {
            $email      = $this->input->post('email', true);
            $password   = $this->input->post('password', true);

            /*User*/
            $this->load->model('user_model');
            if($this->user_model->user_login($email, $password, 2))
            {
                $user_id = $this->user_model->get_id_by_email($email);
                if($user_id===false)
                {
                    $this->session->set_userdata('form_error', '<p>مشکلی در عملیات ورود رخ داده است.</p>');
                    redirect($this->base_url() . "login");
                    exit(0);
                }
                /*Login*/
                $this->load->model('login_model');
                $this->login_model->insert($user_id, $this->time(), $this->user_agent() . " / IP: " . $this->user_ip());
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('user_login', true);
                redirect($this->base_url() . "panel");
                exit(0);
            }
            else
            {
                $this->session->set_userdata('form_error', '<p>مشکلی در عملیات ورود رخ داده است.</p>');
                redirect($this->base_url() . "login");
                exit(0);
            }
        }
    }

	public function forget()
	{
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if($this->check_login())
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();

        $rules = array(
            array(
                'field' =>  'email',
                'label' =>  'ایمیل',
                'rules' =>  'required|valid_email|min_length[5]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'valid_email'=> 'لطفا یک %s معتبر وارد کنید',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "forget");
            exit(0);
        }
        else
        {
            $email      = $this->input->post('email', true);

            /*User*/
            $this->load->model('user_model');
            $user_id = $this->user_model->get_id_by_email($email);
            if($user_id===false)
            {
                $this->session->set_userdata('form_error', '<p>مشکلی در عملیات بازیابی رخ داده است.</p>');
                redirect($this->base_url() . "forget");
                exit(0);
            }
            else
            {
                /* Type 1 : Admin, Type 2 : User */
                if($this->user_model->get_type_by_id($user_id)!=2)
                {
                    $this->session->set_userdata('form_error', '<p>مشکلی در عملیات بازیابی رخ داده است.</p>');
                    redirect($this->base_url() . "forget");
                    exit(0);
                }

                if($this->user_model->user_enable($user_id))
                {
                    /*Old Password*/
                    $old_password = $this->user_model->get_password_by_id($user_id);
                    $this->load->model('old_password_model');
                    $this->old_password_model->insert($user_id, $old_password, $this->time(), $this->user_agent() . " / IP: " . $this->user_ip());

                    /*New Password*/
                    $new_password = $this->time();
                    $this->user_model->change_password($user_id, $new_password);
                    $this->load->model('user_option_model');
                    $this->user_option_model->set_option($user_id, 'need_change_password', 'true');

                    $this->session->set_userdata('form_success', '<p>رمزعبور شما موقتا به ' . '<strong>' . $new_password . '</strong>' . ' تغییر یافت.</p>');
                    redirect($this->base_url() . "login");
                    exit(0);
                }
                else
                {
                    $this->session->set_userdata('form_error', '<p>مشکلی در عملیات بازیابی رخ داده است.</p>');
                    redirect($this->base_url() . "forget");
                    exit(0);
                }
            }
        }
    }

	public function register()
	{
		$this->self_set_url($this->current_url());

        $this->load->helper('url');
        if($this->check_login())
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

		$this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();

        $rules = array(
        	array(
        		'field'	=>	'firstname',
        		'label'	=>	'نام',
        		'rules'	=>	'required|min_length[2]|max_length[100]',
        		'errors'=>	array(
        			'required'	=>	'فیلد %s اجباری می باشد.',
        			'min_length'=>	'طول رشته ی فیلد %s کوتاه می باشد.',
        			'max_length'=>	'فیلد %s طول رشته ی بلندی دارد.'
        		)
        	),
        	array(
        		'field'	=>	'lastname',
        		'label'	=>	'نام خانوادگی',
        		'rules'	=>	'required|min_length[2]|max_length[100]',
        		'errors'=>	array(
        			'required'	=>	'فیلد %s اجباری می باشد.',
        			'min_length'=>	'طول رشته ی فیلد %s کوتاه می باشد.',
        			'max_length'=>	'فیلد %s طول رشته ی بلندی دارد.'
        		)
        	),
        	array(
        		'field'	=>	'email',
        		'label'	=>	'ایمیل',
        		'rules'	=>	'required|valid_email|is_unique[user.email]|min_length[5]|max_length[100]',
        		'errors'=>	array(
        			'required'	=>	'فیلد %s اجباری می باشد.',
        			'valid_email'=>	'لطفا یک %s معتبر وارد کنید',
        			'is_unique'=>	'این کاربر در سیستم موجود می باشد.',
        			'min_length'=>	'طول رشته ی فیلد %s کوتاه می باشد.',
        			'max_length'=>	'فیلد %s طول رشته ی بلندی دارد.'
        		)
        	),
        	array(
        		'field'	=>	'password',
        		'label'	=>	'رمزعبور',
        		'rules'	=>	'required|min_length[5]|max_length[40]',
        		'errors'=>	array(
        			'required'	=>	'فیلد %s اجباری می باشد.',
        			'min_length'=>	'طول رشته ی فیلد %s کوتاه می باشد.',
        			'max_length'=>	'فیلد %s طول رشته ی بلندی دارد.'
        		)
        	)
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "register");
            exit(0);
        }
        else
        {
            $firstname  = $this->input->post('firstname', true);
            $lastname   = $this->input->post('lastname', true);
            $email      = $this->input->post('email', true);
            $password   = $this->input->post('password', true);

            /*User*/
            $this->load->model('user_model');
            $user = $this->user_model->insert($email, $password, $this->time(), 2, 1);
            if(!$user)
            {
                $this->session->set_userdata('form_error', '<p>مشکلی در عملیات ثبت نام رخ داده است.</p>');
                redirect($this->base_url() . "register");
                exit(0);
            }
            else
            {
                $user_id = $this->user_model->get_id_by_email($email);
                if($user_id===false)
                {
                    $this->session->set_userdata('form_error', '<p>مشکلی در عملیات ثبت نام رخ داده است.</p>');
                    redirect($this->base_url() . "register");
                    exit(0);
                }
            }

            /*Person*/
            $this->load->model('person_model');
            $this->person_model->insert($user_id, $firstname, $lastname, null, null, '', '', '');

            /*Avatar*/
            $this->load->model('avatar_model');
            $this->avatar_model->insert($user_id, 'default.png', $this->time(), 1, $this->user_agent() . " / IP: " . $this->user_ip());

            /*User Option*/
            $this->load->model('user_option_model');
            $this->user_option_model->insert($user_id, 'private_page', 'false');
            $this->user_option_model->insert($user_id, 'private_contact', 'false');
            $this->user_option_model->insert($user_id, 'private_avatar', 'false');
            $this->user_option_model->insert($user_id, 'need_change_password', 'false');

            $this->session->set_userdata('form_success', '<p>ثبت نام با موفقیت انجام شد.</p>');
            redirect($this->base_url() . "login");
            exit(0);
        }
	}

	public function search()
	{
		$this->self_set_url($this->current_url());
		echo 'ok';
	}

    public function setting()
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();

        $rules = array(
            array(
                'field' =>  'private_page',
                'label' =>  'نمایش پروفایل',
                'rules' =>  'required',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.'
                )
            ),
            array(
                'field' =>  'private_contact',
                'label' =>  'نمایش اطلاعات تماس',
                'rules' =>  'required',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.'
                )
            ),
            array(
                'field' =>  'private_avatar',
                'label' =>  'نمایش تصویر کاربری',
                'rules' =>  'required',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/setting");
            exit(0);
        }
        else
        {
            $private_page   = $this->input->post('private_page', true);
            $private_contact= $this->input->post('private_contact', true);
            $private_avatar = $this->input->post('private_avatar', true);

            $this->load->model('user_option_model');
            $this->user_option_model->set_option($this->session->userdata('user_id'), "private_page", $private_page);
            $this->user_option_model->set_option($this->session->userdata('user_id'), "private_contact", $private_contact);
            $this->user_option_model->set_option($this->session->userdata('user_id'), "private_avatar", $private_avatar);

            $this->session->set_userdata('form_success', '<p>تغییرات با موفقیت انجام شد.</p>');
            redirect($this->base_url() . "panel/setting");
            exit(0);
        }

    }

    public function newpost()
    {
        $this->self_set_url($this->current_url());
        echo 'ok';
    }

}