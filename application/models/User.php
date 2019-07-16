<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
   	}

    /* User Login */
    public function login($argu) {
      $this->db->where('id', $argu['id']);
      $this->db->where('pw', $argu['pw']);
      $this->db->select("*");
      $this->db->from("user");
      $result = $this->db->get();
      $data = '';
      foreach( $result->result() as $row )
      {
        $data = $row->idx;
      }
      return $data;
    }

}