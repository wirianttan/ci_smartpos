<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index()
    {
        $email = $this->session->userdata['email'];
        $user = $this->User_model->cekDataUser($email);

        $this->load->view('user/index');
    }

}
