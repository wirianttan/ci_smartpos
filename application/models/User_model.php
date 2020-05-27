<?php

class User_model extends CI_model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }


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

        if ($this->_sendEmail($email, $token, 'verify')) {
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


        if ($this->_sendEmail($email, $token, 'forgot')) {
            return (bool) $this->db->insert('user_token', $user_token);
        } else {
            return false;
        }
    }

    private function _sendEmail($email, $token, $type)
    {
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'id.smartpos@gmail.com',
            'smtp_pass' => 'w1r14nt48899',
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->load->library('email');
        $this->email->initialize($config);

        $this->email->from('id.smartpos@gmail.com', 'SmartPOS');
        $this->email->to($email);

        if ($type == 'verify') {

            $this->email->subject('SmartPOS - Account Verification for new account');
            $this->email->message('Click this link to verify your account : <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Activate</a><br><br>The token will be expired in 7 (seven) days.');
        } else {
            if ($type == 'forgot') {

                $this->email->subject('SmartPOS - Reset your password');
                $this->email->message('Click this link to change your password : <a href="' . base_url() . 'auth/resetPassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a><br><br>The token will be expired in 7 (seven) days.');
            }
        }

        $result = (bool) $this->email->send();
        return $result;
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

        $result = (bool) $this->db->update('user');
        return $result;
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

    public function simpanDbUSer($email, $dbname)
    {
        $hostname = 'localhost';
        $username = 'root';
        $userpassword = '';

        $user_database = [
            'email' => $email,
            'hostname' => $hostname,
            'username' => $username,
            'userpassword' => $userpassword,
            'database' => $dbname
        ];

        $this->db->delete('user_database', ['email' => $email]);

        return (bool) $this->db->insert('user_database', $user_database);
    }

    public function activateUser($email, $id)
    {
        $date = date('Y-m-d', strtotime('+30 day'));
        $this->db->set('is_active', 1);
        $this->db->set('expired_at', $date);
        $this->db->where('email', $email);
        $result = (bool) $this->db->update('user');

        $dbname = 'sp' . $id;

        $this->simpanDbUSer($email, $dbname);
        $this->createDB($email, $dbname);

        return $result;
    }

    public function hapusUser($email)
    {
        return (bool) $this->db->delete('user', ['email' => $email]);
    }

    public function hapusTokenUser($email)
    {
        return (bool) $this->db->delete('user_token', ['email' => $email]);
    }

    public function setClientDB($email)
    {
        $result =  $this->db->get_where('user_database', ['email' => $email])->row_array();
        if ($result) {
            $this->session->set_userdata('db_hostname', $result['hostname']);
            $this->session->set_userdata('db_username', $result['username']);
            $this->session->set_userdata('db_userpassword', $result['userpassword']);
            $this->session->set_userdata('db_database', $result['database']);
        } else {
            $this->session->set_userdata('db_hostname', 'localhost');
            $this->session->set_userdata('db_username', 'wirianta_smartpos');
            $this->session->set_userdata('db_userpassword', 'wirianta_smartpos');
            $this->session->set_userdata('db_database', 'wirianta_demo');
        }
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

    public function dbOwner()
    {
        $this->db->close();
        $this->load->database('default', FALSE);
    }

    public function dbClient()
    {
        $config['hostname'] = $this->session->userdata('db_hostname');
        $config['username'] = $this->session->userdata('db_username');
        $config['password'] = $this->session->userdata('db_userpassword');
        $config['database'] =  $this->session->userdata('db_database');
        $config['dbdriver'] = 'mysqli';
        $config['dbprefix'] = '';
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = '';
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $this->db->close();
        $this->load->database($config, FALSE);
    }


    public function createDB($email, $dbname)
    {
        $this->dbforge->create_database($dbname);
        $this->setClientDB($email);
        $this->dbClient();
        $this->load->dbforge();

        $this->dbforge->add_field('id');
        $this->dbforge->add_field("branchcode varchar(5) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("branchname varchar(100) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("address varchar(200) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("city varchar(200) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("phone varchar(20) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("is_active int(1) NOT NULL DEFAULT 1");
        $this->dbforge->add_field("created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        $this->dbforge->add_key('branchcode', TRUE);
        $this->dbforge->add_key('branchname');
        $this->dbforge->create_table('m_branch');

        $this->dbforge->add_field('id');
        $this->dbforge->add_field("itemcode varchar(30) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("itemname varchar(100) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("statusactive int(1) NOT NULL DEFAULT 1");
        $this->dbforge->add_field("pricenego int(1) NOT NULL DEFAULT 0");
        $this->dbforge->add_field("qtygabung int(1) NOT NULL DEFAULT 1");
        $this->dbforge->add_field("printlabel int(1) NOT NULL DEFAULT 0");
        $this->dbforge->add_field("categoryname varchar(50) NOT NULL DEFAULT 'Umum'");
        $this->dbforge->add_field("uom varchar(20) NOT NULL DEFAULT 'pcs'");
        $this->dbforge->add_field("qtydecimal int(1) NOT NULL DEFAULT 0");
        $this->dbforge->add_field("sellingprice double NOT NULL DEFAULT 0");
        $this->dbforge->add_field("created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        $this->dbforge->add_key('itemcode', TRUE);
        $this->dbforge->add_key('itemname');
        $this->dbforge->create_table('m_item');

        $this->dbforge->add_field('id');
        $this->dbforge->add_field("branchcode varchar(5) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("itemcode varchar(30) NOT NULL DEFAULT '-'");
        $this->dbforge->add_field("sellingprice double NOT NULL DEFAULT 0");
        $this->dbforge->add_field("created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        $this->dbforge->add_key('branchcode', TRUE);
        $this->dbforge->add_key('itemcode', TRUE);
        $this->dbforge->create_table('m_branch_pl');

        $this->dbOwner();
    }
}
