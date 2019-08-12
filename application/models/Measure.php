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
			$this->db->set('date', date("y/m/d"));
			$this->db->set('temperature', 0);
			$this->db->set('humidity', 0);
			$this->db->insert("measure");
			$result = $this->db->get();

			$data = array(
				'hb' => 0,
				'data' => date("y/m/d")
			);

			return array(
				'status' => API_SUCCESS, 
				'message' => 'Success',
				'data' => json_encode($data)
			);
   		}
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

    /* 측정하기 버튼 클릭 */
    public function flag($argu) {

      $this->error_log("[models/Measure/flag] ENTER");
      if(empty($argu['flag'])) {
        return array(
			'status' => API_FAILURE, 
			'message' => 'Fail'        
        );
      } else {
        
		$this->error_log($argu['user_idx']);
		$this->error_log($argu['flag']);

		if(!$this->check_flag($argu)) {
			$this->db->set('user_idx', $argu['user_idx']);
			$this->db->set('flag', $argu['flag']);
			$this->db->insert("measure_flag");
		} else {
			$this->db->set('flag', $argu['flag']);
			$this->db->where('user_idx', $argu['user_idx']);
			$this->db->update("measure_flag");
		}

		return array(
			'status' => API_SUCCESS, 
			'message' => 'SUCCESS'
		);
        
        
      }
    }

    /* 측정한 경험이 있는지 */
    private function check_flag($argu) {
		$this->db->where('user_idx', $argu['user_idx']);
		$this->db->select("*");
		$this->db->from("measure_flag");
		$result = $this->db->get();
		return $result->num_rows();
    }

}