<?php

class User_model extends CI_model
{

    public function tambahDataUser()
    {
        $data = [
            'name' => htmlspecialchars($this->input->post('name', true)),
            'email' => htmlspecialchars($this->input->post('email', true)),
            'image' => 'default.jpg',
            'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
            'role_id' => 2,
            'is_active' => 1,
            'date_created' => time()
        ];

        $this->db->insert('user', $data);
    }

    public function cekDataUser($email)
    {
       $data =  $this->db->get_where('user', ['email' => $email])->row_array();

       return $data;
    }

    public function flashErrorMessage($message, $success)
    {
        if ($success) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success" role="alert">' 
                . $message .
                '</div>');
            } else {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger" role="alert">' 
                    . $message .
                    '</div>');

        }
        
        redirect('auth');
    }
}
