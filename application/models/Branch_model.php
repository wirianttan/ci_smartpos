<?php

class Branch_model extends CI_model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function getAllBranchData()
    {
        $this->User_model->dbClient();

        $dataBranch =  $this->db->order_by('branchname')->get_where('m_branch')->result_array();

        $this->User_model->dbOwner();

        return $dataBranch;
    }

    public function getBranch($id)
    {
        $this->User_model->dbClient();
        $dataBranch =  $this->db->get_where('m_branch', ['id' => $id])->row_array();
        $this->User_model->dbOwner();

        return $dataBranch;
    }


    public function cekDuplikasiBranch()
    {
        $branchcode = $this->input->post('branchcode');
        $this->User_model->dbClient();
        $dataBranch =  $this->db->get_where('m_branch', ['branchcode' => $branchcode])->row_array();
        $this->User_model->dbOwner();

        return $dataBranch;
    }

    public function tambahBranch()
    {
        $is_active = $this->input->post('is_active', true);

        if (!$is_active) {
            $is_active = 0;
        } else {
            $is_active = 1;
        }

        $data = [
            'branchcode' => $this->input->post('branchcode', true),
            'branchname' => $this->input->post('branchname', true),
            'address' => $this->input->post('address', true),
            'city' => $this->input->post('city', true),
            'phone' => $this->input->post('phone', true),
            'is_active' => $is_active
        ];

        $this->User_model->dbClient();
        $result = $this->db->insert('m_branch', $data);
        $this->User_model->dbOwner();

        return $result;
    }
}
