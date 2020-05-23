<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('User_model');

        if (!$this->session->userdata['email']) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'My Profile';
        $email = $this->session->userdata['email'];
        $data['user'] = $this->User_model->cekDataUser($email);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('profile/index', $data);
        $this->load->view('templates/footer');
    }

    public function editProfile()
    {
        $redir = 'profile';

        $data['title'] = 'Edit My Profile';
        $email = $this->session->userdata['email'];
        $data['user'] = $this->User_model->cekDataUser($email);

        $this->form_validation->set_rules('name', 'Full Name', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('profile/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $data['user']['name'] = $this->input->post('name');
            if ($this->User_model->ubahDataUser($data)) {
                $message = 'Congratulation! <br>Your profile has been updated.';
                $this->User_model->flashErrorMessage($message, True, $redir);
            } else {
                $redir = 'profile/editProfile';
                $message = 'Failed to update your profile. <br>Please try again.';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        }
    }

    public function changePassword()
    {

        $redir = 'profile/changepassword';
        $data['title'] = 'Change Password';
        $email = $this->session->userdata['email'];
        $data['user'] = $this->User_model->cekDataUser($email);

        $this->form_validation->set_rules('current_password', 'Current Password', 'required|trim');
        $this->form_validation->set_rules('new_password1', 'New Password', 'required|trim|min_length[2]|matches[new_password2]');
        $this->form_validation->set_rules('new_password2', 'Confirm New Password', 'required|trim|matches[new_password1]');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('profile/changepassword', $data);
            $this->load->view('templates/footer');
        } else {
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password1');

            if (!password_verify($current_password, $data['user']['password'])) {
                $message = 'Wrong current password';
                $this->User_model->flashErrorMessage($message, False, $redir);
            } else {
                if ($current_password == $new_password) {
                    $message = 'New password cannot be same with current password';
                    $this->User_model->flashErrorMessage($message, False, $redir);
                } else {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $this->User_model->ubahPasswordUser($email, $password_hash);

                    $message = 'Congratulation! <br>Your password has been changed.';
                    $this->User_model->flashErrorMessage($message, True, $redir);
                }
            }
        }
    }
}
