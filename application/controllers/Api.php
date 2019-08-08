<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	/* 메인 페이지 */
	public function index()
	{
		$this->load->helper('url');
		$this->load->view('welcome_message');
	}

	/* 로그인 API */
	public function login() {
		$this->load->model('User');
		$result = $this->User->login(array(
			'id' => $_GET['id'],
			'pw' => md5($_GET['pw'])
		));
		echo json_encode($result);
	}

	/* 회원가입 API */
	public function join() {
		error_reporting(0);
		$this->error_log("정음이가 들어왔어요");
		$_POST = json_decode(file_get_contents('php://input'), true);

		$this->load->model('User');

		$this->error_log("id: " + $_POST['id']);
		$this->error_log("pw: " + $_POST['pw']);
		$this->error_log("name: " + $_POST['name']);
		$this->error_log("gender: " + $_POST['gender']);
		$this->error_log("birth: " + $_POST['birth']);

		$result = $this->User->insert(array(
			'id' => $_POST['id'],
			'pw' => md5($_POST['pw']),
			'name' => $_POST['name'],
			'gender' => $_POST['gender'],
			'birth' => $_POST['birth']
		));

		$this->error_log("정음이가 나갔어요");
		echo json_encode($result);
	}

	/* 회원가입 API 2 */
	public function join2($input) {
		error_reporting(0);

		$this->error_log("정음이가 들어왔어요");

		$this->load->model('User');
		$result = $this->User->insert(array(
			'id' =>  element('id', $input, null),
			'pw' => element('pw', $input, null),
			'name' => element('name', $input, null),
			'gender' => element('gender', $input, null),
			'birth' => element('birth', $input, null)
		));


		echo json_encode($result);
	}

	/* 측정하기 API */
	public function measure() {
		$this->load->model('Measure');
		$result = $this->Measure->insert(array(
			'user_idx' => $_POST['user_idx'],
			'period' => $_POST['period'],
			'video' => $_POST['video']
		));
		echo json_encode($result);
	}

	/* 측정리스트 API */
	public function measures() {
		$this->load->model('Measure');
		$result = $this->Measure->list_search(array(
			'user_idx' => $_GET['user_idx']
		));

		echo json_encode($result);
	}

	/* 날씨 정보 API */
	public function weather() {
		$url = "http://api.openweathermap.org/data/2.5/weather?q=Seoul,kr&units=metric&APPID=4049256d888776cd6b2a0d093b861255";
		 
		$ch = curl_init();
		 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		curl_setopt($ch, CURLOPT_URL, $url);
		 
		$result=curl_exec($ch);
		 
		$json_results = json_decode($result, true);
		 
		// print_r($json_results);
		// print_r($json_results['main']);
		$data = array(
			'temp' => $json_results['main']['temp'],
			'reh' => $json_results['main']['humidity']
		);

		if($data) {
			$result = array(
				'status' => 200,
				'message' => 'Success',
				'data' => $data
			);
		} else {
			$result = array(
				'status' => 400,
				'message' => 'Fail',
				'data' => null
			);
		}
		
		echo json_encode($result);
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
