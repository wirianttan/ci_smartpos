<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');

        if (!$this->session->userdata['email']) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'SmartPOS';
        $email = $this->session->userdata['email'];
        $data['user'] = $this->User_model->cekDataUser($email);

        // var_dump($user);
        // die();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
    }
}
