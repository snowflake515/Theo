<?php

class ReportLogModel extends CI_Model {

    var $table = "ReportLog";
    var $key = "ReportLog_ID";

    function __construct() {
        parent::__construct();
        $this->db = $this->load->database('data', TRUE);
    }

    function select_db() {
        return $this->db->from($this->table);
    }

    function insert($data) {
        $this->db->insert($this->table, $data);
    }

    function update($id, $data) {
        $this->db->where($this->key, $id);
        $this->db->update($this->table, $data);
    }

    function delete($id) {
        $this->db->where($this->key, $id);
        $this->db->delete($this->table);
    }

}
