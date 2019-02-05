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

                $this->load->model('user_option_model');
                $need_change_password = $this->user_option_model->get_option($user_id, 'need_change_password');
                if($need_change_password=='true')
                    $this->session->set_userdata('need_change_password', true);
                else
                    $this->session->set_userdata('need_change_password', false);
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

            $this->load->model('contact_model');
            $this->contact_model->insert($user_id, 1, "", $this->time());
            $this->contact_model->insert($user_id, 2, "", $this->time());
            $this->contact_model->insert($user_id, 3, "", $this->time());
            $this->contact_model->insert($user_id, 4, "", $this->time());

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
            ),
            array(
                'field' =>  'password',
                'label' =>  'رمز عبور فعلی',
                'rules' =>  'min_length[5]|max_length[40]',
                'errors'=>  array(
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'new_password',
                'label' =>  'رمز عبور تازه',
                'rules' =>  'min_length[5]|max_length[40]',
                'errors'=>  array(
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'new_repassword',
                'label' =>  'تکرار رمز عبور تازه',
                'rules' =>  'min_length[5]|max_length[40]|matches[new_password]',
                'errors'=>  array(
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.',
                    'matches'   =>  'فیلد %s هماهنگ نیست.'
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
            $password       = $this->input->post('password', true);
            $new_password   = $this->input->post('new_password', true);
            $new_repassword = $this->input->post('new_repassword', true);

            $this->load->model('user_option_model');
            $this->user_option_model->set_option($this->session->userdata('user_id'), "private_page", $private_page);
            $this->user_option_model->set_option($this->session->userdata('user_id'), "private_contact", $private_contact);
            $this->user_option_model->set_option($this->session->userdata('user_id'), "private_avatar", $private_avatar);

            if(!empty($password) && !empty($new_password) && !empty($new_repassword))
            {
                $this->load->model('user_model');
                if($this->user_model->authorize_password($this->session->userdata('user_id'), $password))
                {
                    $this->load->model('old_password_model');
                    $this->old_password_model->insert($this->session->userdata('user_id'), $password, $this->time(), $this->user_agent() . " / IP: " . $this->user_ip());

                    $this->user_model->change_password($this->session->userdata('user_id'), $new_password);
                    $this->load->model('user_option_model');
                    $this->user_option_model->set_option($this->session->userdata('user_id'), 'need_change_password', 'false');
                    $this->session->set_userdata('form_success', '<p>رمز عبور با موفقیت تغییر یافت.</p><p>تغییرات با موفقیت انجام شد.</p>');
                }
                else
                {
                    $this->session->set_userdata('form_success', '<p>تغییرات با موفقیت انجام شد.</p>');
                }
            }
            else
            {
                $this->session->set_userdata('form_success', '<p>تغییرات با موفقیت انجام شد.</p>');
            }

            redirect($this->base_url() . "panel/setting");
            exit(0);
        }

    }

    public function newpost()
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
                'field' =>  'post_content',
                'label' =>  'متن پست',
                'rules' =>  'required|min_length[3]|max_length[10000]',
                'errors'=>  array(
                    'required'    =>  'فیلد %s اجباری می باشد.',
                    'min_length'  =>  'محدودیت کاراکتر فیلد %s را رعایت نکردید.',
                    'max_length'  =>  'محدودیت کاراکتر فیلد %s را رعایت نکردید.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel");
            exit(0);
        }
        else
        {
            $post_content   = $this->input->post('post_content', true);

            $config['upload_path']          = './upload/file/';
            $config['allowed_types']        = 'gif|jpg|jpeg|png';
            $config['max_size']             = 0;
            $config['encrypt_name']         = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('post_file'))
            {
                $data = array('upload_data' => $this->upload->data());
                $this->load->model('file_model');
                $this->file_model->insert($this->session->userdata('user_id'), $this->upload->data('file_name'), $this->time(), 1, 1, $this->user_agent() . " / IP: " . $this->user_ip());
                $file_id = $this->file_model->find_id_by_file_name($this->upload->data('file_name'));
                if($file_id===false)
                    $file_id = null;
            }
            else
            {
                $file_id = null;
            }

            $this->load->model('post_model');
            $this->post_model->insert($this->session->userdata('user_id'), $file_id, $post_content, $this->time(), $this->time(), 1, $this->user_agent() . " / IP: " . $this->user_ip());

            $this->session->set_userdata('form_success', '<p>نوشته ی شما با موفقیت ثبت  شد.</p>');
            redirect($this->base_url() . "panel");
            exit(0);
        }
    }

    public function editperson()
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
                'field' =>  'firstname',
                'label' =>  'نام',
                'rules' =>  'required|min_length[2]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'lastname',
                'label' =>  'نام خانوادگی',
                'rules' =>  'required|min_length[2]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'country_id',
                'label' =>  'کشور',
                'rules' =>  'required|numeric',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'numeric'   =>  'فیلد %s معتبر نیست'
                )
            ),
            array(
                'field' =>  'zip_code',
                'label' =>  'کدپستی',
                'rules' =>  'numeric|min_length[2]|max_length[20]',
                'errors'=>  array(
                    'numeric'   =>  'فیلد %s معتبر نیست',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'birthday',
                'label' =>  'تاریخ تولد',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/person");
            exit(0);
        }
        else
        {
            $firstname      = $this->input->post('firstname', true);
            $lastname       = $this->input->post('lastname', true);
            $country_id     = $this->input->post('country_id', true);
            $zip_code       = $this->input->post('zip_code', true);
            $birthday       = $this->input->post('birthday', true);

            if(!empty($birthday))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$birthday)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/person");
                    exit(0);
                }
            }

            $this->load->model('country_model');
            $country_name = $this->country_model->get_country_name($country_id);
            if($country_name === false)
                $country_id = null;

            $this->load->model('person_model');
            $this->person_model->update($this->session->userdata('user_id'), $firstname, $lastname, $country_id, $zip_code, $birthday);

            $this->session->set_userdata('form_success', '<p>تغییرات با موفقیت انجام شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/person");
            exit(0);
        }
    }

    public function editbio()
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
                'field' =>  'biography',
                'label' =>  'بیوگرافی',
                'rules' =>  'required|min_length[3]|max_length[255]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 255 کاراکتر می باشد.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/bio");
            exit(0);
        }
        else
        {
            $biography   = $this->input->post('biography', true);

            $this->load->model('person_model');
            $this->person_model->update_user_biography($this->session->userdata('user_id'), $biography);

            $this->session->set_userdata('form_success', '<p>بیوگرافی شما با موفقیت ویرایش شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/bio");
            exit(0);
        }
    }

    public function add_experience()
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
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/experience");
            exit(0);
        }
        else
        {
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/experience");
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/experience");
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->insert($this->session->userdata('user_id'),  1, $title, $content, $start_date, $end_date, $this->time());

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/experience");
            exit(0);
        }
    }

    public function edit_experience()
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
                'field' =>  'id',
                'label' =>  'آیدی',
                'rules' =>  'required|numeric',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'numeric'   =>  'فیلد %s باید عددی باشد.'
                )
            ),
            array(
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $id = $this->input->post('id', true);
            if(empty($id) || is_null($id))
            {
                redirect($this->base_url() . "panel/profile/edit/experience");
                exit(0);
            }

            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/experience/edit/" . $id);
            exit(0);
        }
        else
        {
            $id         = $this->input->post('id', true);
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/experience/edit/" . $id);
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/experience/edit/" . $id);
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->update($this->session->userdata('user_id'), $id, $title, $content, $start_date, $end_date);

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/experience/edit/" . $id);
            exit(0);
        }
    }

    public function delete_experience($experience_id)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }
        $this->load->database();

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

        $this->load->model('user_item_model');
        $this->user_item_model->delete($this->session->userdata('user_id'), $experience_id);

        $this->session->set_userdata('database_action', '<p>عملیات با موفقیت انچام شد.</p>');
        redirect($this->base_url() . "panel/profile/edit/experience");
        exit(0);
    }

    public function add_education()
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
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/education");
            exit(0);
        }
        else
        {
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/education");
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/education");
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->insert($this->session->userdata('user_id'),  2, $title, $content, $start_date, $end_date, $this->time());

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/education");
            exit(0);
        }
    }

    public function edit_education()
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
                'field' =>  'id',
                'label' =>  'آیدی',
                'rules' =>  'required|numeric',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'numeric'   =>  'فیلد %s باید عددی باشد.'
                )
            ),
            array(
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $id = $this->input->post('id', true);
            if(empty($id) || is_null($id))
            {
                redirect($this->base_url() . "panel/profile/edit/education");
                exit(0);
            }

            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/education/edit/" . $id);
            exit(0);
        }
        else
        {
            $id         = $this->input->post('id', true);
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/education/edit/" . $id);
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/education/edit/" . $id);
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->update($this->session->userdata('user_id'), $id, $title, $content, $start_date, $end_date);

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/education/edit/" . $id);
            exit(0);
        }
    }

    public function delete_education($education_id)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }
        $this->load->database();

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

        $this->load->model('user_item_model');
        $this->user_item_model->delete($this->session->userdata('user_id'), $education_id);

        $this->session->set_userdata('database_action', '<p>عملیات با موفقیت انچام شد.</p>');
        redirect($this->base_url() . "panel/profile/edit/education");
        exit(0);
    }

    public function add_skills()
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
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/skills");
            exit(0);
        }
        else
        {
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/skills");
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/skills");
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->insert($this->session->userdata('user_id'),  3, $title, $content, $start_date, $end_date, $this->time());

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/skills");
            exit(0);
        }
    }

    public function edit_skills()
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
                'field' =>  'id',
                'label' =>  'آیدی',
                'rules' =>  'required|numeric',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'numeric'   =>  'فیلد %s باید عددی باشد.'
                )
            ),
            array(
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $id = $this->input->post('id', true);
            if(empty($id) || is_null($id))
            {
                redirect($this->base_url() . "panel/profile/edit/skills");
                exit(0);
            }

            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/skills/edit/" . $id);
            exit(0);
        }
        else
        {
            $id         = $this->input->post('id', true);
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/skills/edit/" . $id);
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/skills/edit/" . $id);
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->update($this->session->userdata('user_id'), $id, $title, $content, $start_date, $end_date);

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/skills/edit/" . $id);
            exit(0);
        }
    }

    public function delete_skills($skills_id)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }
        $this->load->database();

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

        $this->load->model('user_item_model');
        $this->user_item_model->delete($this->session->userdata('user_id'), $skills_id);

        $this->session->set_userdata('database_action', '<p>عملیات با موفقیت انچام شد.</p>');
        redirect($this->base_url() . "panel/profile/edit/skills");
        exit(0);
    }

    public function add_project()
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
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/project");
            exit(0);
        }
        else
        {
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/project");
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/project");
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->insert($this->session->userdata('user_id'),  4, $title, $content, $start_date, $end_date, $this->time());

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/project");
            exit(0);
        }
    }

    public function edit_project()
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
                'field' =>  'id',
                'label' =>  'آیدی',
                'rules' =>  'required|numeric',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'numeric'   =>  'فیلد %s باید عددی باشد.'
                )
            ),
            array(
                'field' =>  'title',
                'label' =>  'عنوان',
                'rules' =>  'required|min_length[3]|max_length[100]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'حداقل طول %s 3 کاراکتر می باشد.',
                    'max_length'=>  'حداکثر طول %s 100 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'content',
                'label' =>  'توضیحات',
                'rules' =>  'max_length[1000]',
                'errors'=>  array(
                    'max_length'=>  'حداکثر طول %s 1000 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'start_date',
                'label' =>  'تاریخ شروع',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'end_date',
                'label' =>  'تاریخ پایان',
                'rules' =>  'max_length[10]',
                'errors'=>  array(
                    'max_length' =>  'حداکثر طول %s 10 کاراکتر می باشد.'
                )
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $id = $this->input->post('id', true);
            if(empty($id) || is_null($id))
            {
                redirect($this->base_url() . "panel/profile/edit/project");
                exit(0);
            }

            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/profile/edit/project/edit/" . $id);
            exit(0);
        }
        else
        {
            $id         = $this->input->post('id', true);
            $title      = $this->input->post('title', true);
            $content    = $this->input->post('content', true);
            $start_date = $this->input->post('start_date', true);
            $end_date   = $this->input->post('end_date', true);

            if(!empty($start_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$start_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/project/edit/" . $id);
                    exit(0);
                }
            }

            if(!empty($end_date))
            {
                if (!preg_match("/^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/",$end_date)) {
                    $this->session->set_userdata('form_error', "<p>تاریخ ورودی معتبر نیست.</p>");
                    redirect($this->base_url() . "panel/profile/edit/project/edit/" . $id);
                    exit(0);
                }
            }

            $this->load->model('user_item_model');
            $this->user_item_model->update($this->session->userdata('user_id'), $id, $title, $content, $start_date, $end_date);

            $this->session->set_userdata('form_success', '<p>رکرود جدید با موفقیت ثبت شد.</p>');
            redirect($this->base_url() . "panel/profile/edit/project/edit/" . $id);
            exit(0);
        }
    }

    public function delete_project($project_id)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }
        $this->load->database();

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

        $this->load->model('user_item_model');
        $this->user_item_model->delete($this->session->userdata('user_id'), $project_id);

        $this->session->set_userdata('database_action', '<p>عملیات با موفقیت انچام شد.</p>');
        redirect($this->base_url() . "panel/profile/edit/project");
        exit(0);
    }

    public function editsocial()
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
                'field' =>  'linkedin',
                'label' =>  'لینک لینکدین',
                'rules' =>  'valid_url|max_length[500]',
                'errors'=>  array(
                    'valid_url'  =>  'فید %s معتبر نیست.',
                    'max_length'=>  'حداکثر طول %s 255 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'twitter',
                'label' =>  'لینک توییتر',
                'rules' =>  'valid_url|max_length[500]',
                'errors'=>  array(
                    'valid_url'  =>  'فید %s معتبر نیست.',
                    'max_length'=>  'حداکثر طول %s 255 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'telegram',
                'label' =>  'لینک تلگرام',
                'rules' =>  'valid_url|max_length[500]',
                'errors'=>  array(
                    'valid_url'  =>  'فید %s معتبر نیست.',
                    'max_length'=>  'حداکثر طول %s 255 کاراکتر می باشد.'
                )
            ),
            array(
                'field' =>  'skype',
                'label' =>  'لینک اسکایپ',
                'rules' =>  'valid_url|max_length[500]',
                'errors'=>  array(
                    'valid_url'  =>  'فید %s معتبر نیست.',
                    'max_length'=>  'حداکثر طول %s 255 کاراکتر می باشد.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('social_error', validation_errors());
            redirect($this->base_url() . "panel/profile");
            exit(0);
        }
        else
        {
            $linkedin   = $this->input->post('linkedin', true);
            $twitter    = $this->input->post('twitter', true);
            $telegram   = $this->input->post('telegram', true);
            $skype      = $this->input->post('skype', true);

            $this->load->model('contact_model');
            $this->contact_model->update($this->session->userdata('user_id'), 1, $linkedin);
            $this->contact_model->update($this->session->userdata('user_id'), 2, $twitter);
            $this->contact_model->update($this->session->userdata('user_id'), 3, $telegram);
            $this->contact_model->update($this->session->userdata('user_id'), 4, $skype);

            $this->session->set_userdata('social_success', '<p>عملیات موفق بود.</p>');
            redirect($this->base_url() . "panel/profile");
            exit(0);
        }
    }

    public function newavatar()
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
        
        $post_content   = $this->input->post('post_content', true);

        $config['upload_path']          = './upload/avatar/';
        $config['allowed_types']        = 'jpg|jpeg|png';
        $config['max_size']             = 0;
        $config['encrypt_name']         = TRUE;

        $this->load->library('upload', $config);
        if ($this->upload->do_upload('avatar_file'))
        {
            $data = array('upload_data' => $this->upload->data());
            $this->load->model('avatar_model');
            $this->avatar_model->disable_current_avatar($this->session->userdata('user_id'));
            $this->avatar_model->insert($this->session->userdata('user_id'), $this->upload->data('file_name'), $this->time(), 1, $this->user_agent() . " / IP: " . $this->user_ip());
            $this->session->set_userdata('avatar_success', '<p>عملیات موفق بود.</p>');
        }

        redirect($this->base_url() . "panel/profile");
        exit(0);
    }

    public function change_password()
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
                'field' =>  'password',
                'label' =>  'رمز عبور فعلی',
                'rules' =>  'required|min_length[5]|max_length[40]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'new_password',
                'label' =>  'رمز عبور تازه',
                'rules' =>  'required|min_length[5]|max_length[40]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.'
                )
            ),
            array(
                'field' =>  'new_repassword',
                'label' =>  'تکرار رمز عبور تازه',
                'rules' =>  'required|min_length[5]|max_length[40]|matches[new_password]',
                'errors'=>  array(
                    'required'  =>  'فیلد %s اجباری می باشد.',
                    'min_length'=>  'طول رشته ی فیلد %s کوتاه می باشد.',
                    'max_length'=>  'فیلد %s طول رشته ی بلندی دارد.',
                    'matches'   =>  'فیلد %s هماهنگ نیست.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/change_password");
            exit(0);
        }
        else
        {
            $password       = $this->input->post('password', true);
            $new_password   = $this->input->post('new_password', true);
            $new_repassword = $this->input->post('new_repassword', true);


            $this->load->model('user_model');
            if($this->user_model->authorize_password($this->session->userdata('user_id'), $password))
            {
                $this->load->model('old_password_model');
                 $this->old_password_model->insert($this->session->userdata('user_id'), $password, $this->time(), $this->user_agent() . " / IP: " . $this->user_ip());

                $this->user_model->change_password($this->session->userdata('user_id'), $new_password);
                $this->load->model('user_option_model');
                $this->user_option_model->set_option($this->session->userdata('user_id'), 'need_change_password', 'false');
                $this->session->set_userdata('need_change_password', false);
                redirect($this->base_url() . "panel");
                exit(0);
            }

            redirect($this->base_url() . "panel/change_password");
            exit(0);
        }
    }

    public function add_connect($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($user_key) || is_null($user_key))
        {
            show_404();
            exit(0);
        }

        $previous = "javascript:history.go(-1)";
        if(isset($_SERVER['HTTP_REFERER'])) {
            $previous = $_SERVER['HTTP_REFERER'];
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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('block_model');
        if(!$this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        {
            $this->load->model('connections_model');
            if(!$this->connections_model->is_connection($this->session->userdata('user_id'), $user['id']) && !$this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']))
            {
                $this->connections_model->insert($this->session->userdata('user_id'), $user['id'], $this->time(), 2, 1);
                $this->connections_model->insert($user['id'], $this->session->userdata('user_id'), $this->time(), 2, -1);
                $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');

                if(stripos($previous, "panel") !== false)
                {
                    redirect($this->base_url() . "panel/#user_suggest_5");
                    exit(0);
                }
                else
                {
                    redirect($this->base_url() . "user/" . $user_key);
                    exit(0);
                }
            }
            else
            {
                $this->session->set_userdata('profile_error', '<p>این عملیات قبلا یکبار انجام شده است.</p>');
                redirect($this->base_url() . "user/" . $user_key);
                exit(0);
            }
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>ابتدا این کاربر را آنبلاک کنید.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function delete_connect($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($user_key) || is_null($user_key))
        {
            show_404();
            exit(0);
        }

        $previous = "javascript:history.go(-1)";
        if(isset($_SERVER['HTTP_REFERER'])) {
            $previous = $_SERVER['HTTP_REFERER'];
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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('block_model');
        if(!$this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        {
            $this->load->model('connections_model');
            if($this->connections_model->is_connection($this->session->userdata('user_id'), $user['id']) && $this->connections_model->is_connection($this->session->userdata('user_id'), $user['id']))
            {
                $this->connections_model->delete_connection($this->session->userdata('user_id'), $user['id']);
                $this->connections_model->delete_connection($user['id'], $this->session->userdata('user_id'));
                $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');

                if(stripos($previous, "connections") !== false)
                {
                    redirect($this->base_url() . "panel/profile/connections");
                    exit(0);
                }
                else
                {
                    redirect($this->base_url() . "user/" . $user_key);
                    exit(0);
                }
            }
            else
            {
                $this->session->set_userdata('profile_error', '<p>عملیات با شکست روبرو  شد.</p>');
                redirect($this->base_url() . "user/" . $user_key);
                exit(0);
            }
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>ابتدا این کاربر را آنبلاک کنید.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function confirm_connect($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($user_key) || is_null($user_key))
        {
            show_404();
            exit(0);
        }

        $previous = "javascript:history.go(-1)";
        if(isset($_SERVER['HTTP_REFERER'])) {
            $previous = $_SERVER['HTTP_REFERER'];
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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('block_model');
        if(!$this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        {
            $this->load->model('connections_model');
            if($this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']) && $this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']) && !$this->connections_model->is_requester_connection($this->session->userdata('user_id'), $user['id']))
            {
                $this->connections_model->confirm_connection($this->session->userdata('user_id'), $user['id']);
                $this->connections_model->confirm_connection($user['id'], $this->session->userdata('user_id'));
                $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');

                if(stripos($previous, "connections") !== false)
                {
                    redirect($this->base_url() . "panel/profile/connections");
                    exit(0);
                }
                else
                {
                    redirect($this->base_url() . "user/" . $user_key);
                    exit(0);
                }
            }
            else
            {
                $this->session->set_userdata('profile_error', '<p>شما مجاز به انجام این عملیات نیستید.</p>');
                redirect($this->base_url() . "user/" . $user_key);
                exit(0);
            }
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>ابتدا این کاربر را آنبلاک کنید.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function unconfirm_connect($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($user_key) || is_null($user_key))
        {
            show_404();
            exit(0);
        }

        $previous = "javascript:history.go(-1)";
        if(isset($_SERVER['HTTP_REFERER'])) {
            $previous = $_SERVER['HTTP_REFERER'];
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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('block_model');
        if(!$this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        {
            $this->load->model('connections_model');
            if($this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']) && $this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']) && !$this->connections_model->is_requester_connection($this->session->userdata('user_id'), $user['id']))
            {
                $this->connections_model->delete_connection($this->session->userdata('user_id'), $user['id']);
                $this->connections_model->delete_connection($user['id'], $this->session->userdata('user_id'));
                $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');

                if(stripos($previous, "connections") !== false)
                {
                    redirect($this->base_url() . "panel/profile/connections");
                    exit(0);
                }
                else
                {
                    redirect($this->base_url() . "user/" . $user_key);
                    exit(0);
                }
            }
            else
            {
                $this->session->set_userdata('profile_error', '<p>شما مجاز به انجام این عملیات نیستید.</p>');
                redirect($this->base_url() . "user/" . $user_key);
                exit(0);
            }
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>ابتدا این کاربر را آنبلاک کنید.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function delete_request($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('block_model');
        if(!$this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        {
            $this->load->model('connections_model');
            if($this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']) && $this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']) && $this->connections_model->is_requester_connection($this->session->userdata('user_id'), $user['id']))
            {
                $this->connections_model->delete_connection($this->session->userdata('user_id'), $user['id']);
                $this->connections_model->delete_connection($user['id'], $this->session->userdata('user_id'));
                $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');
                redirect($this->base_url() . "user/" . $user_key);
                exit(0);
            }
            else
            {
                $this->session->set_userdata('profile_error', '<p>شما مجاز به انجام این عملیات نیستید.</p>');
                redirect($this->base_url() . "user/" . $user_key);
                exit(0);
            }
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>ابتدا این کاربر را آنبلاک کنید.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function report($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('report_model');
        if(!$this->report_model->check_duplicate($this->session->userdata('user_id') ,$user['id'], 1))
        {
            $this->report_model->insert($this->session->userdata('user_id'), $user['id'], 1, $this->time());

            if($this->report_model->report_user_count($user['id'], 1) > 200)
            {
                $this->user_model->disable_user($user_id);
            }

            $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>این عملیات قبلا یکبار انجام شده است.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function block($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('block_model');
        if(!$this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        {
            $this->block_model->block($this->session->userdata('user_id'), $user['id'], $this->time());

            $this->load->model('connections_model');
            if($this->connections_model->is_connection($this->session->userdata('user_id'), $user['id']) || $this->connections_model->is_respond_connection($this->session->userdata('user_id'), $user['id']))
            {
                $this->connections_model->delete_connection($this->session->userdata('user_id'), $user['id']);
                $this->connections_model->delete_connection($user['id'], $this->session->userdata('user_id'));
            }

            $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>این عملیات قبلا یکبار انجام شده است.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function unblock($user_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

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

        if($this->session->userdata('user_id') == $user['id'])
        {
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }

        /* Report User */
        $this->load->model('block_model');
        if($this->block_model->is_block($this->session->userdata('user_id'), $user['id']))
        {
            $this->block_model->unblock($this->session->userdata('user_id'), $user['id']);
            $this->session->set_userdata('profile_success', '<p>عملیات با موفقیت انجام شد.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
        else
        {
            $this->session->set_userdata('profile_error', '<p>این عملیات قبلا یکبار انجام شده است.</p>');
            redirect($this->base_url() . "user/" . $user_key);
            exit(0);
        }
    }

    public function like_post($post_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($post_key))
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        /* Load User Page */
        $this->load->helper('security');
        $post_key = trim(xss_clean($post_key));
        $this->load->model('post_model');
        $post = $this->post_model->find_post($post_key);
        if($post===false)
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->model('block_model');
        if($this->block_model->is_block($this->session->userdata('user_id'), $post['user_id']) || $this->block_model->is_block($post['user_id'], $this->session->userdata('user_id'))) {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->model('connections_model');
        if( ($this->session->userdata('user_id')!=$post['user_id']) && (!$this->connections_model->is_connection($this->session->userdata('user_id'), $post['user_id']) && !$this->connections_model->is_connection($post['user_id'], $this->session->userdata('user_id'))) )
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->model('like_model');
        if(!$this->like_model->is_like($post['id'], $this->session->userdata('user_id'))) {
            $this->like_model->insert($this->session->userdata('user_id'), $post['id'], $this->time());
            redirect($this->base_url() . "panel#" . $post_key);
            exit(0);
        }
        
        redirect($this->base_url() . "panel");
        exit(0);
    }

    public function dislike_post($post_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($post_key))
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        /* Load User Page */
        $this->load->helper('security');
        $post_key = trim(xss_clean($post_key));
        $this->load->model('post_model');
        $post = $this->post_model->find_post($post_key);
        if($post===false)
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->model('block_model');
        if($this->block_model->is_block($this->session->userdata('user_id'), $post['user_id']) || $this->block_model->is_block($post['user_id'], $this->session->userdata('user_id'))) {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->model('connections_model');
        if( ($this->session->userdata('user_id')!=$post['user_id']) && (!$this->connections_model->is_connection($this->session->userdata('user_id'), $post['user_id']) && !$this->connections_model->is_connection($post['user_id'], $this->session->userdata('user_id'))) )
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        $this->load->model('like_model');
        if($this->like_model->is_like($post['id'], $this->session->userdata('user_id'))) {
            $this->like_model->delete($this->session->userdata('user_id'), $post['id']);
            redirect($this->base_url() . "panel#" . $post_key);
            exit(0);
        }
        
        redirect($this->base_url() . "panel");
        exit(0);
    }

    public function edit_post($post_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($post_key))
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        /* Load User Page */
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
        $this->load->library('form_validation');
        $this->load->database();

        $rules = array(
            array(
                'field' =>  'post_content',
                'label' =>  'متن پست',
                'rules' =>  'required|min_length[3]|max_length[10000]',
                'errors'=>  array(
                    'required'    =>  'فیلد %s اجباری می باشد.',
                    'min_length'  =>  'محدودیت کاراکتر فیلد %s را رعایت نکردید.',
                    'max_length'  =>  'محدودیت کاراکتر فیلد %s را رعایت نکردید.'
                )
            )
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_userdata('form_error', validation_errors());
            redirect($this->base_url() . "panel/post/edit/" . $post_key);
            exit(0);
        }
        else
        {
            $post_content   = $this->input->post('post_content', true);

            $this->load->model('post_model');
            $this->post_model->update($post['id'], $this->session->userdata('user_id'), $post_content, $this->time(), $this->user_agent() . " / IP: " . $this->user_ip());

            $this->session->set_userdata('form_success', '<p>نوشته با موفقیت ویرایش شد.</p>');
            redirect($this->base_url() . "panel/post/edit/" . $post_key);
            exit(0);
        }
    }

    public function delete_post($post_key)
    {
        $this->self_set_url($this->current_url());

        $this->load->helper('url');
        if(!$this->check_login())
        {
            redirect($this->base_url() . "login");
            exit(0);
        }

        if(empty($post_key))
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        /* Load User Page */
        $this->load->helper('security');
        $post_key = trim(xss_clean($post_key));
        $this->load->model('post_model');
        $post = $this->post_model->find_post($post_key);
        if($post===false)
        {
            redirect($this->base_url() . "panel");
            exit(0);
        }

        if($this->session->userdata('user_id')==$post['user_id'])
        {
            $this->post_model->delete($post['id'], $this->session->userdata('user_id'));
            $this->session->set_userdata('post_delete', '<p>نوشته ی موردنظر با موفقیت حذف شد.</p>');
            redirect($this->base_url() . "panel");
            exit(0);
        }
        
        redirect($this->base_url() . "panel");
        exit(0);
    }

}