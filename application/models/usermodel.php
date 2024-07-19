<?php

class UserModel extends CI_Model {

  var $table = "Users";
  var $key = "ID";

  function __construct() {
    parent::__construct();
    $this->db_user = $this->load->database('user', TRUE);
  }

  function insert($data) {
    $this->db_user->trans_begin();
    $this->db_user->insert($this->table, $data);
    $this->db_user->trans_commit();
  }

  function update($id, $data) {
    $this->db_user->where($this->key, $id);
    $this->db_user->update($this->table, $data);

  }

  function delete($id) {

    $this->db_user->where($this->key, $id);
    $this->db_user->delete($this->table);

  }

  function get_all() {
    return $this->db_user->get($this->table);
  }

  function get_by_id($id) {
    $this->db_user->where($this->key, $id);
    $this->db_user->from($this->table);
    return $this->db_user->get();
  }

  function get_by_field($field, $val, $other_condition = null) {
    $this->db_user->where($field, $val);
    if ($other_condition != null) {
      $this->db_user->where($other_condition);
    }
    $this->db_user->from($this->table);
    return $this->db_user->get();
  }

  function checkPIN($id, $pin) {
    $this->db_user->where('ID', $id);
    $this->db_user->where('UserPin', $pin);
    $this->db_user->from($this->table);
    $get = $this->db_user->get();
    $num = $get->num_rows();
    $user = $get->row();
    return ($user && $num == 1) ? 'true' : 'false';
  }

  function check_login($userid, $password, $client_number) {
    $userid = strtolower(trim($userid));
    $exp = array('org1', 'awacs1'); 

    $this->db_user->where('LOWER(User_Id)', strtolower($userid));
    $this->db_user->where('Password', $this->encript($password));
    if(!in_array($userid, $exp)){
      $this->db_user->where('Org_Id', $client_number);
    }
    $this->db_user->where('Status', 'A');
    $this->db_user->from($this->table);
    $get = $this->db_user->get();
    $num = $get->num_rows();
    $user = $get->row();
    return ($user && $num == 1) ? $user : FALSE;
  }

  function encript($str = NULL) {
    $key = str_split("QWERTYUIOPASDFGHJKL;");
    $kpost = 0;
    $result = NULL;
    if ($str != NULL) {
      foreach (str_split($str) as $v) {
        $result.=ord($v) + ord($key[$kpost]);
        $result.="|";
        if ($kpost >= 19) {
          $kpost = 1;
        } else {
          $kpost++;
        }
      }
    }
    return $result;
  }

  function deccrypt($str = NULL) {
    $key = str_split("QWERTYUIOPASDFGHJKL;");
    $kpost = 0;
    $result = NULL;
    if ($str != NULL) {
      foreach (explode('|', $str) as $v) {
        if ($v) {
          $k = $v - ord($key[$kpost]);
          $result.=chr($k);
          if ($kpost >= 19) {
            $kpost = 1;
          } else {
            $kpost++;
          }
        }
      }
    }
    return $result;
  }

  function get_by_userid($id) {
    $this->db_user->where('User_Id', $id);
    $this->db_user->from($this->table);
    return $this->db_user->get()->row();
  }

}
