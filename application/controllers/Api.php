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
		$this->load->model('User');
		$result = $this->User->insert(array(
			'id' => $_POST['id'],
			'pw' => md5($_POST['pw']),
			'name' => $_POST['name'],
			'gender' => $_POST['gender'],
			'birth' => $_POST['birth']
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
		$result = array(
			'temp' => $json_results['main']['temp'],
			'reh' => $json_results['main']['humidity']
		);

		echo json_encode($result);
	}

}
