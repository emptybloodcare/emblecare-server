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
		$url = "http://www.kma.go.kr/wid/queryDFS.jsp?gridx=67&gridy=100";
		$result = simplexml_load_file($url);
		$list = array();

		$location= iconv("UTF-8","euc-kr",$result->header->title); //예보지역
		$results = $result->body;
		$bl_data='';
		foreach($results->data as $item) {
			if(!$bl_data) {
				// $temp=$item->temp; //현재온도
				// $sky=iconv("UTF-8","euc-kr",$item->wfKor); //날씨상태(맑음,구름조금,구름많음,흐림,비,눈/비,눈)
				$temp = $item;

				print_r(($temp));
				// $bl_data=true;
				echo "<br>";
			}
		}
		print_r(($temp));
	}
	


}
