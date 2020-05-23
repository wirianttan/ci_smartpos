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

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->User_model->cekDataUser($email);
        $redir = 'auth';

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
                    $this->User_model->flashErrorMessage($message, False, $redir);
                }
            } else {
                $message = 'The email has not been activated!';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        } else {
            $message = 'The email is not registered!';
            $this->User_model->flashErrorMessage($message, False, $redir);
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
        $redir = 'auth';

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
            if ($this->User_model->tambahDataUser()) {
                $message = 'Congratulation! <br>Your account has been created. <br>Please check your email to activate your account.';
                $this->User_model->flashErrorMessage($message, True, $redir);
            } else {
                $redir = 'auth/registration';
                $message = 'Failed to create your account. Please try again';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        }
    }

    public function verify()
    {
        $redir = 'auth';

        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->User_model->cekDataUser($email);

        if ($user) {
            $user_token = $this->User_model->cekDataTokenUser($email);
            if ($user_token['token'] == $token) {
                if (time() - $user_token['date_created'] < (60 * 60 * 24 * 7)) {

                    $this->User_model->activateUser($email);
                    $this->User_model->hapusTokenUser($email);

                    $message = 'The account is activated.';
                    $this->User_model->flashErrorMessage($message, True, $redir);
                } else {

                    $this->User_model->hapusTokenUser($email);
                    $this->User_model->hapusUser($email);

                    $message = 'Token expire.';
                    $this->User_model->flashErrorMessage($message, False, $redir);
                }
            } else {
                $message = 'Activation failed! Token is not valid.';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        } else {
            $message = 'Activation failed! Email is not valid.';
            $this->User_model->flashErrorMessage($message, False, $redir);
        }
    }

    public function logout()
    {
        $redir = 'auth';

        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $message = 'You have been logged out.';
        $this->User_model->flashErrorMessage($message, True, $redir);
    }

    public function forgotPassword()
    {
        $redir = 'auth/forgotPassword';
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'SmartPOS - Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $user = $this->User_model->cekDataUser($email);

            if ($user) {
                if ($user['is_active'] == 1) {
                    if ($this->User_model->lupaPassword()) {
                        $message = 'Congratulation! <br>Please check your email to reset your password.';
                        $this->User_model->flashErrorMessage($message, True, $redir);
                    } else {
                        $message = 'Failed to process the account. Please try again';
                        $this->User_model->flashErrorMessage($message, False, $redir);
                    }
                } else {
                    $message = 'The account is not activated.';
                    $this->User_model->flashErrorMessage($message, False, $redir);
                }
            } else {
                $message = 'Email is not registered.';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        }
    }

    public function resetPassword()
    {
        $redir = 'auth';

        $email = $this->input->get('email');
        $token = $this->input->get('token');
        $user = $this->User_model->cekDataUser($email);

        if ($user) {
            $user_token = $this->User_model->cekDataTokenUser($email);
            if ($user_token['token'] == $token) {
                if (time() - $user_token['date_created'] < (60 * 60 * 24 * 7)) {
                    $this->session->set_userdata('reset_email', $email);

                    // $data['title'] = 'SmartPOS - Change Password';
                    // $this->load->view('templates/auth_header', $data);
                    // $this->load->view('auth/change-password', $email);
                    // $this->load->view('templates/auth_footer');
                    $this->changePassword();
                } else {

                    $this->User_model->hapusTokenUser($email);

                    $message = 'Token expire.';
                    $this->User_model->flashErrorMessage($message, False, $redir);
                }
            } else {
                $message = 'Reset password failed! Token is not valid.';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        } else {
            $message = 'Reset password failed! Email is not valid.';
            $this->User_model->flashErrorMessage($message, False, $redir);
        }
    }

    public function changePassword()
    {
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
        }

        $redir = 'auth';


        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[2]|matches[password2]', [
            'matches' => 'Password not match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'SmartPOS - Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $new_password = $this->input->post('password1');
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            if ($this->User_model->ubahPasswordUser($email, $password_hash)) {

                $this->session->unset_userdata('reset_email');

                $message = 'Congratulation! <br>Your password has been changed.';
                $this->User_model->flashErrorMessage($message, True, $redir);
            } else {
                $redir = 'auth/changePassword';
                $message = 'Failed to change your password. Please try again';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        }
    }
}
