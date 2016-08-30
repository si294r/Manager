<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manager_model extends CI_Model {

    var $db = null;
    var $table = 'admin';
    var $column = array('username', 'password', 'status', 'roles');
    var $order = array('username' => 'asc');
    private $is_development = false;

    public function __construct() {
        parent::__construct();
    }

    public function init_db($is_development = false) {
        $this->load->helper('mongodb');
        $this->db = get_mongodb($is_development);
        $this->is_development = $is_development;
    }

    private function _get_filter() {
        $filter = [];
        foreach ($this->column as $item) {
            if ($_POST['search']['value']) {
                $filter[] = array($item => array('$regex' => $_POST['search']['value']));
            }
        }
        if (count($filter) > 0) {
            return array('$or' => $filter);
        } else {
            return array();
        }
    }

    private function _get_sort() {
        $order = $this->order;
        if (isset($_POST['order'])) {
            $key = $this->column[$_POST['order']['0']['column']];
            $value = $_POST['order']['0']['dir'] == 'desc' ? -1 : 1;
        } else {
            $key = key($order);
            $value = $order[key($order)] == 'desc' ? -1 : 1;
        }
        return array($key => $value);
    }

    function get_datatables() {
        $filter = $this->_get_filter();
        $sort = $this->_get_sort();
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $options = ['sort' => $sort, 'limit' => (int) $_POST['length'], 'skip' => (int) $_POST['start']];
        } else {
            $options = ['sort' => $sort];
        }
        $documents = $this->db->admin->find($filter, $options);
        return bson_documents_to_array($documents);
    }

    function count_filtered() {
        $filter = $this->_get_filter();
        return $this->db->admin->count($filter);
    }

    public function count_all() {
        return $this->db->admin->count([]);
    }

    public function get_by_id($id) {
        $document = $this->db->admin->findOne(['_id' => bson_oid($id)]);
        return bson_document_to_array($document);
    }

    public function save($data) {
        return $this->db->admin->insertOne($data);
    }

    public function update_by_id($id, $data) {
        $document = $this->db->admin->updateOne(['_id' => bson_oid($id)], ['$set' => $data]);
        return bson_document_to_array($document);
    }

    public function delete_by_id($id) {
        $this->db->admin->deleteOne(['_id' => bson_oid($id)]);
    }

    public function is_development() {
        return $this->is_development;
    }

    public function duplicate_username($username, $id = '') {
        $document = $this->db->admin->findOne(['username' => $username]);
        if ($id == '') {
            return is_object($document); // cek duplicate for insert
        } else {
            return is_object($document) && $document->_id != $id; // cek duplicate for update
        }
    }

}
