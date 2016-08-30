<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manager extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['signin'])) {
            redirect('signin');
        }
        $this->load->model('manager_model', 'manager');
        $this->manager->init_db(false);
    }

    public function index() {
        $this->load->view('manager_view');
    }

    public function ajax_list() {
        $list = $this->manager->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $manager) {
            $no++;
            $row = array();
            $row[] = $manager['username'];
            $row[] = $manager['password'];
            $row[] = $manager['status'];
            $row[] = isset($manager['roles']) ? $manager['roles'] : '';

            if ($manager['username'] == 'admin') {
                $row[] = '';
            } else {
                //add html for action
                $row[] = '<a class="btn btn-sm btn-primary" href="javascript:" title="Edit" onclick="edit_user(' . "'" . $manager['_id'] . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
                  <a class="btn btn-sm btn-danger" href="javascript:" title="Hapus" onclick="delete_user(' . "'" . $manager['_id'] . "'" . ')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
            }

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->manager->count_all(),
            "recordsFiltered" => $this->manager->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_edit($id) {
        $data = $this->manager->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add() {
        if (!$this->manager->duplicate_username($this->input->post('username'))) {
            $data = array(
                'username' => $this->input->post('username'),
                'password' => md5($this->input->post('password')),
                'status' => $this->input->post('status'),
                'roles' => $this->input->post('roles')
            );
            $insert = $this->manager->save($data);
            echo json_encode(array("status" => TRUE));
        } else {
            echo json_encode(array("status" => FALSE, "message" => "Username already exists!"));
        }
    }

    public function ajax_update() {
        if (!$this->manager->duplicate_username($this->input->post('username'), $this->input->post('_id'))) {
            $password = $this->input->post('password');
            $password = $this->input->post('password_old') == $password ? $password : md5($password);
            $data = array(
                'username' => $this->input->post('username'),
                'password' => $password,
                'status' => $this->input->post('status'),
                'roles' => $this->input->post('roles')
            );
            $this->manager->update_by_id($this->input->post('_id'), $data);
            echo json_encode(array("status" => TRUE));
        } else {
            echo json_encode(array("status" => FALSE, "message" => "Username already exists!"));
        }
    }

    public function ajax_delete($id) {
        $this->manager->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
