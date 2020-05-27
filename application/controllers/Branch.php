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

        $config['base_url'] = base_url() . 'branch/index';
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-end">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li">';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li">';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li">';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li">';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li">';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li">';
        $config['attributes'] = array('class' => 'page-link');

        $keyword = $this->input->post('keyword');

        if ($keyword) {
            $config['total_rows'] = $this->Branch_model->countAllBranchesByKeyword($keyword);
            $this->pagination->initialize($config);
            $data['start'] = $this->uri->segment(3);
            $data['branch'] = $this->Branch_model->getBranchDataByKeyword($keyword, $config['per_page'], $data['start']);
        } else {
            $config['total_rows'] = $this->Branch_model->countAllBranches();
            $this->pagination->initialize($config);
            $data['start'] = $this->uri->segment(3);
            $data['branch'] = $this->Branch_model->getAllBranchDataByRange($config['per_page'], $data['start']);
        }

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

        $this->form_validation->set_rules('branchname', 'Name', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('address', 'Address', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('city', 'City', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim|max_length[202]');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('branch/edit', $data);
            $this->load->view('templates/footer');
        } else {
            if ($this->Branch_model->ubahBranch()) {
                $redir = 'branch';
                $message = 'The outlet data has been updated.';
                $this->User_model->flashErrorMessage($message, True, $redir);
            } else {
                $redir = 'branch/edit';
                $message = 'Failed to update the outlet! Please try again.';
                $this->User_model->flashErrorMessage($message, False, $redir);
            }
        }
    }

    public function deleteBranch($id)
    {
        $redir = 'branch';

        if ($this->Branch_model->hapusBranch($id)) {
            $message = 'The outlet has been deleted.';
            $this->User_model->flashErrorMessage($message, True, $redir);
        } else {
            $message = 'Failed to delete the outlet! Please try again.';
            $this->User_model->flashErrorMessage($message, False, $redir);
        }
    }
}
