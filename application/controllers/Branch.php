<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Branch extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->library('form_validation');
        $this->load->model('User_model');
        $this->load->model('Branch_model');

        if (!$this->session->userdata['email']) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Tables - Outlets';
        $email = $this->session->userdata['email'];
        $data['user'] = $this->User_model->cekDataUser($email);
        $data['branch'] = $this->Branch_model->getAllBranchData();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('branch/index', $data);
        $this->load->view('templates/footer');
    }

    public function addBranch()
    {
        $data['title'] = 'Tables - Outlets - Add';
        $email = $this->session->userdata['email'];
        $data['user'] = $this->User_model->cekDataUser($email);

        $this->User_model->dbClient();
        $this->form_validation->set_rules('branchcode', 'Code', 'required|trim|max_length[5]|is_unique[m_branch.branchcode]');
        $this->form_validation->set_rules('branchname', 'Name', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('address', 'Address', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('city', 'City', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim|max_length[202]');

        if ($this->form_validation->run() == false) {
            $this->User_model->dbOwner();
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('branch/add', $data);
            $this->load->view('templates/footer');
        } else {
            $this->User_model->dbOwner();

            if ($this->Branch_model->tambahBranch()) {
                $redir = 'branch';
                $message = 'Congratulation! New outlet has been added.';
                $this->User_model->flashErrorMessage($message, True, $redir);
            } else {
                $redir = 'branch/addbranch';
                $message = 'Failed to add new outlet! Please try again.';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        }
    }

    public function editBranch($id)
    {
        $data['title'] = 'Tables - Outlets - Edit';
        $email = $this->session->userdata['email'];
        $data['user'] = $this->User_model->cekDataUser($email);

        $data['branch'] = $this->Branch_model->getBranch($id);

        $this->User_model->dbClient();
        $this->form_validation->set_rules('branchname', 'Name', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('address', 'Address', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('city', 'City', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim|max_length[202]');

        if ($this->form_validation->run() == false) {
            $this->User_model->dbOwner();
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('branch/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->User_model->dbOwner();

            if ($this->Branch_model->editBranch()) {
                $redir = 'branch';
                $message = 'Congratulation! The outlet has been updated.';
                $this->User_model->flashErrorMessage($message, True, $redir);
            } else {
                $redir = 'branch/editbranch';
                $message = 'Failed to update the outlet! Please try again.';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        }
    }
}
