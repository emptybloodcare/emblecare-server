<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
   	}

   	public function test() {
   		$this->db->select("user_id");
   		$this->db->from("user");
   		$result = $this->db->get();
   		foreach( $result->result() as $row )
		{
        	echo $row->user_id;
		}
   	}
}