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
      if(empty($argu['id']) || empty($argu['pw']) {
        return array('status' => API_EMPTY_PARAMS, 'data' => null);
      } else {
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

     /* User Join */
    public function insert($argu) {
      if(empty($argu['id']) || empty($argu['pw']) || empty($argu['name']) || empty($argu['gender']) || empty($argu['gender'])) {
        return API_EMPTY_PARAMS;
      } else {
        $this->db->set('id', $argu['id']);
        $this->db->set('pw', $argu['pw']);
        $this->db->set('name', $argu['name']);
        $this->db->set('gender', $argu['gender']);
        $this->db->set('birth', $argu['birth']);
        $this->db->insert("user");
        $result = $this->db->get();
      
        return $this->db->insert_id();
      }
    }
}