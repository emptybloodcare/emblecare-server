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
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function login() {
		$this->load->model('User');
		$result = $this->User->login(array(
			'id' => $_POST['id'],
			'pw' => $_POST['pw']
		));
		if($result) {
			$data = array(
				'status' => 200,
				'message' => '로그인 성공',
				'data' => $result
			);
		} else {
			$data = array(
				'status' => 400,
				'message' => '로그인 실패',
				'data' => $result
			);
		}
		echo json_encode($data);
	}
}
