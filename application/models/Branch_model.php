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

        $dataBranch =  $this->db->order_by('branchname')->get('m_branch')->result_array();

        $this->User_model->dbOwner();

        return $dataBranch;
    }

    public function getAllBranchDataByRange($limit, $start)
    {
        $this->User_model->dbClient();

        $dataBranch =  $this->db->order_by('branchname')->get('m_branch', $limit, $start)->result_array();

        $this->User_model->dbOwner();

        return $dataBranch;
    }


    public function countAllBranches()
    {
        $this->User_model->dbClient();

        $dataBranch =  $this->db->get('m_branch')->num_rows();

        $this->User_model->dbOwner();

        return $dataBranch;
    }

    public function countAllBranchesByKeyword($keyword)
    {
        $this->User_model->dbClient();

        $this->db->like('branchcode', $keyword);
        $this->db->or_like('branchname', $keyword);
        $this->db->or_like('address', $keyword);
        $dataBranch =  $this->db->get('m_branch')->num_rows();

        $this->User_model->dbOwner();

        return $dataBranch;
    }

    public function getBranchDataByKeyword($keyword, $limit, $start)
    {
        $this->User_model->dbClient();

        $this->db->like('branchcode', $keyword);
        $this->db->or_like('branchname', $keyword);
        $this->db->or_like('address', $keyword);
        $dataBranch =  $this->db->order_by('branchname')->get('m_branch', $limit, $start)->result_array();
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

    public function ubahBranch()
    {

        $is_active = $this->input->post('is_active', true);

        if (!$is_active) {
            $is_active = 0;
        } else {
            $is_active = 1;
        }

        $this->User_model->dbClient();

        $this->db->set('branchname', $this->input->post('branchname', true));
        $this->db->set('address', $this->input->post('address', true));
        $this->db->set('city', $this->input->post('city', true));
        $this->db->set('phone', $this->input->post('phone', true));
        $this->db->set('is_active', $is_active);
        $this->db->where('branchcode', $this->input->post('branchcode', true));
        $result = (bool) $this->db->update('m_branch');

        $this->User_model->dbOwner();

        return $result;
    }

    public function hapusBranch($id)
    {
        $this->User_model->dbClient();

        $this->db->where('id', $id);
        $result = (bool) $this->db->delete('m_branch');

        $this->User_model->dbOwner();

        return $result;
    }
}
