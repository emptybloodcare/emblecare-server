<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Measure extends CI_Model {

	function __construct()
    {
        parent::__construct();
        $this->load->database();
   	}

   	/* 측정하기 */
   	public function insert($argu) {
   		if(empty($argu['period']) || empty($argu['video'])) {
			return array(
				'status' => API_FAILURE, 
				'message' => 'Fail',
				'data' => null
			);
   		} else {
   			$this->db->set('period', $argu['period']);
			$this->db->set('hb', 0);
			$this->db->set('user_idx', $argu['user_idx']);
			$this->db->set('date', date("Y-m-d"));
			$this->db->set('temperature', 0);
			$this->db->set('humidity', 0);
			$this->db->insert("measure");
			$result = $this->db->get();

			return array(
				'status' => API_SUCCESS, 
				'message' => 'Success',
				'data' => null
			);
   		}
   	}

   	/* 측정결과 */
    public function get($argu) {
    	return array(
			'status' => API_SUCCESS, 
			'message' => 'Success',
			'data' => null
		);
    }

    /* 측정 리스트 */
    public function list_search($argu) {
    	$this->db->where('user_idx', $argu['user_idx']);
        $this->db->select("*");
        $this->db->from("measure");
        $result = $this->db->get();
        $data = [];
        foreach( $result->result() as $row )
        {
          // $data = $row->idx;
        }
        return array(
			'status' => API_SUCCESS, 
			'message' => 'Success',
			'data' => $data
		);
    }

}