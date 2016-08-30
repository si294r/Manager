<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    var $table = 'admin';
    var $column = array('username', 'password', 'status');

    public function signin($username, $password) {

        $this->load->helper('mongodb');
           
        $db = get_mongodb_auth();

        $document = $db->admin->findOne([
            'username' => $username,
            'password' => md5($password),
            'status' => 'active'
        ]);
        
        return bson_document_to_array($document);
    }

}
