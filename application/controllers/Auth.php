<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
        // $active_group = 'db1';
        // $this->db1 = $this->load->database("db1", True);
    }

    public function index()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $data['title'] = 'SmartPOS - Login Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            $this->_login();
        }
    }

    private function _login() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->User_model->cekDataUser($email);

        if ($user) {

            if ($user['is_active'] == 1) {

                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id]']
                    ];
                    $this->session->set_userdata($data);
                    redirect('user');

                } else { 
                    $message = 'The password is invalid!';
                    $this->User_model->flashErrorMessage($message, False);
                }

            } else {
                $message = 'The email has not been activated!';
                $this->User_model->flashErrorMessage($message, False);
            }

        } else {
            $message = 'The email is not registered!';
            $this->User_model->flashErrorMessage($message, False);
        }
    }

    public function registration()
    {

        // $config['hostname'] = 'localhost';
        // $config['username'] = 'root';
        // $config['password'] = '';
        // $config['database'] = 'smartpos1';
        // $config['dbdriver'] = 'mysqli';
        // $config['dbprefix'] = '';
        // $config['pconnect'] = FALSE;
        // $config['db_debug'] = TRUE;
        // $config['cache_on'] = FALSE;
        // $config['cachedir'] = '';
        // $config['char_set'] = 'utf8';
        // $config['dbcollat'] = 'utf8_general_ci';
        // $this->db->close();
        // $this->load->database($config, FALSE);

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'The email has already registered!',
            'valid_email' => 'Invalid email format!'
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[2]|matches[password2]', [
            'matches' => 'Password not match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');


        if ($this->form_validation->run() == false) {
            $data['title'] = 'SmartPOS User Registration';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $this->User_model->tambahDataUser();
            
            $message = 'Congratulation! <br>Your account has been created. <br>Please check your email to activate your account.';
            $this->User_model->flashErrorMessage($message, True);
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $message = 'You have been logged out.';
        $this->User_model->flashErrorMessage($message, True);
        
    }
}
