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
   		if(0) {
			return array(
				'status' => API_FAILURE, 
				'message' => 'Fail',
				'data' => null
			);
   		} else {
   			$weather = $this->get_weather();
   			print_r($weather);

   			// file upload
   			$file = $argu['video'];
   			$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/video';
   			$tmp_name = $file["tmp_name"];
			$name = date("YmdHis").'_'.$file["name"];
			echo "$uploadDir/$name";
			move_uploaded_file($tmp_name , "$uploadDir/$name");



   			$this->db->set('period', $argu['period']);
			$this->db->set('hb', 0);
			$this->db->set('user_idx', $argu['user_idx']);
			$this->db->set('date', date("y/m/d"));
			$this->db->set('temperature', $weather['temp']);
			$this->db->set('humidity', $weather['reh']);
			$this->db->insert("measure");
			$result = $this->db->get();

			$data = array(
				'hb' => 0,
				'data' => date("y/m/d")
			);

			return array(
				'status' => API_SUCCESS, 
				'message' => 'Success',
				'data' => $data
			);
   		}
   	}

   	public function get_weather() {
   		$url = "http://api.openweathermap.org/data/2.5/weather?q=Seoul,kr&units=metric&APPID=4049256d888776cd6b2a0d093b861255";
		 
		$ch = curl_init();
		 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		curl_setopt($ch, CURLOPT_URL, $url);
		 
		$result=curl_exec($ch);
		 
		$json_results = json_decode($result, true);
		 
		$data = array(
			'temp' => $json_results['main']['temp'],
			'reh' => $json_results['main']['humidity']
		);
		return $data;
   	}

    /* 측정 리스트 */
    public function list_search($argu) {
    	$this->db->where('user_idx', $argu['user_idx']);
        $this->db->select("*");
        $this->db->from("measure");
        $result = $this->db->get();
        $data = [];
        if($result->num_rows()) {
        	foreach( $result->result() as $row )
	        {
	        	$temp = array(
	        		'idx' => $row->idx,
	        		'hb' => $row->hb,
	        		'period' => $row->period,
	        		'date' => $row->date
	        	);
	        	array_push($data, $temp);
	        }
	        return array(
				'status' => API_SUCCESS, 
				'message' => 'Success',
				'data' => $data,
				'dataNum' => $result->num_rows()
			);
        } else {
        	return array(
				'status' => 204, 
				'message' => '측정결과가 존재하지 않습니다.',
				'data' => $data
			);
        }
        
    }

    /* 측정하기 버튼 클릭 */
    public function flag($argu) {
      $this->error_log("[models/Measure/flag] ENTER");
      if(empty($argu['user_idx'])) {
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

    /* 로그 */
    public function error_log($msg)
    {
		$log_filename = "{$_SERVER['DOCUMENT_ROOT']}/logs/error_log";
		$now        = getdate();
		$today      = $now['year']."/".$now['mon']."/".$now['mday'];
		$now_time   = $now['hours'].":".$now['minutes'].":".$now['seconds'];
		$now        = $today." ".$now_time;
		$filep = fopen($log_filename, "a");
		if(!$filep) {
		die("can't open log file : ". $log_filename);
		}
		fputs($filep, "{$now} : {$msg}\n\r");
		fclose($filep);
    }

}