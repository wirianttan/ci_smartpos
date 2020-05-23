<?php

class User_model extends CI_model
{

    public function tambahDataUser()
    {
        $email = $this->input->post('email', true);

        $data = [
            'name' => htmlspecialchars($this->input->post('name', true)),
            'email' => htmlspecialchars($email),
            'image' => 'default.jpg',
            'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
            'role_id' => 2,
            'is_active' => 0,
            'date_created' => time()
        ];

        $token = base64_encode(random_bytes(32));
        // $token = htmlspecialchars($token);

        $user_token = [
            'email' => $email,
            'token' => $token,
            'date_created' => time()
        ];


        if ($this->_sendEmail($token, 'verify')) {
            if ($this->db->insert('user', $data)) {
                return (bool) $this->db->insert('user_token', $user_token);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function lupaPassword()
    {
        $email = $this->input->post('email', true);

        if ($this->cekDataTokenUser($email)) {
            $this->hapusTokenUser($email);
        }

        $token = base64_encode(random_bytes(32));

        $user_token = [
            'email' => $email,
            'token' => $token,
            'date_created' => time()
        ];


        if ($this->_sendEmail($token, 'forgot')) {
            return (bool) $this->db->insert('user_token', $user_token);
        } else {
            return false;
        }
    }

    private function _sendEmail($token, $type)
    {
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'id.smartpos@gmail.com',
            'smtp_pass' => 'www690316',
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->load->library('email');
        $this->email->initialize($config);

        $this->email->from('id.smartpos@gmail.com', 'SmartPOS');
        $this->email->to($this->input->post('email'));

        if ($type == 'verify') {

            $this->email->subject('SmartPOS - Account Verification for new account');
            $this->email->message('Click this link to verify your account : <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Activate</a><br><br>The token will be expired in 7 (seven) days.');
        } else {
            if ($type == 'forgot') {

                $this->email->subject('SmartPOS - Reset your password');
                $this->email->message('Click this link to change your password : <a href="' . base_url() . 'auth/resetPassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a><br><br>The token will be expired in 7 (seven) days.');
            }
        }

        // if ($this->email->send()) {
        //     return true;
        // } else {
        //     return false;
        // }
        return (bool) $this->email->send();
    }

    public function ubahPasswordUser($email, $password_hash)
    {

        $this->db->set('password', $password_hash);
        $this->db->where('email', $email);

        return (bool) $this->db->update('user');
    }

    public function ubahDataUser($data)
    {
        $upload_image = $_FILES['image']['name'];

        if ($upload_image) {
            $config['upload_path']          = './assets/img/profile/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 2048;
            $config['encrypt_name']         = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('image')) {
                $old_image = $data['user']['image'];
                if ($old_image != 'default.jpg') {
                    unlink(FCPATH . 'assets/img/profile/' . $old_image);
                }

                $new_image = $this->upload->data('file_name');
                $this->db->set('image', $new_image);
            }
        }

        $this->db->set('name', $data['user']['name']);
        $this->db->where('email', $data['user']['email']);

        return (bool) $this->db->update('user');
    }

    public function cekDataUser($email)
    {
        $data =  $this->db->get_where('user', ['email' => $email])->row_array();

        return $data;
    }

    public function cekDataTokenUser($email)
    {

        $data =  $this->db->get_where('user_token', ['email' => $email])->row_array();

        return $data;
    }

    public function activateUser($email)
    {
        $date = date('Y-m-d', strtotime('+30 day'));
        $this->db->set('is_active', 1);
        $this->db->set('expired_at', $date);
        $this->db->where('email', $email);

        return (bool) $this->db->update('user');
    }

    public function hapusUser($email)
    {
        return (bool) $this->db->delete('user', ['email' => $email]);
    }

    public function hapusTokenUser($email)
    {
        return (bool) $this->db->delete('user_token', ['email' => $email]);
    }

    public function flashErrorMessage($message, $success, $redir)
    {
        if ($success) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success" role="alert">'
                    . $message .
                    '</div>'
            );
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger" role="alert">'
                    . $message .
                    '</div>'
            );
        }

        redirect($redir);
    }
}
