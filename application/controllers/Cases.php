<?php

/**
	Created on : 2015-07-25
	Created By : Sunita Mistry
	Purpose : Controller file containing functions to save Inquiries , add Users etc 
	Filename : Cases.php
**/

defined('BASEPATH') OR exit('No direct script access allowed');

class Cases extends CI_Controller {

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
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	 function __construct()
    {
        parent::__construct();
        // Call the Model constructor
        $this->load->model('Case_model');
    }

	public function index()
	{
		$this->load->view('layout/content');
	}
	
	/* Function to load Case Form */
	public function caseForm()
	{
		//$getCustomers = $this->Case_model->getCustomers();
		$data['content'] = 'case_form';
		$this->load->view('layout/content',$data);
	}

	/* Function to load Add User Form */
	public function addUser()
	{
		$data['content'] = 'add_user';
		$this->load->view('layout/content',$data);
	}

	/* Function to save New user info */
	public function saveUser()
	{
		$error = array();
		
		/** Check unique Username **/
		if(strlen($this->input->post('user_name')) > 0 ){
			$chk_uname = $this->Case_model->isUniqueUname($this->input->post('user_name'));
			if($chk_uname == 1)
			$error['err_uname'] = 'Username already exist';
		}

		/** Check unique User Email **/
		if(strlen($this->input->post('user_email')) > 0){
			$chk_email = $this->Case_model->isUniqueEmail($this->input->post('user_email'));
			if($chk_email == 1)
			$error['err_email'] = 'Email Id already exist';
		}

		/** If no error exist **/
		if(count($error) == 0 ){

			$user_type = $this->input->post('user_type');
			$max = $this->Case_model->maxUserVal($user_type);
			$max = ($max == 0) ? '001' : ((strlen($max) == 1) ? ('00'.($max+1)) : ('0'.($max+1)));
			if($user_type == 4) $code = 'A'.$max ;
			else if($user_type == 5) $code = 'C'. $max;
			else $code = 'W'. $max;
			$data = $this->input->post();
			$data = array_merge(array("code"=>$code),$data);
			$this->Case_model->saveUser($data);
			echo 1;
		}else
		echo json_encode($error);
		exit;

	}

	/** To get Customers by filter **/
	public function getCustomer()
	{
		$json = array();
		$results = $this->Case_model->getCustomers($_REQUEST['value']);
		if($results != 0){
			foreach ($results as $result) {
				$json[] = array(
					'user_id' => $result['id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		echo json_encode($json);
	}

	/** To get Cusomer Info by Cusomer ID **/
	public function getCustomerVal()
	{
		$json = array();
		if(isset($_POST['cust_id'])){
			$results = $this->Case_model->getCustomerVal($_POST['cust_id']);
			if($results != 0){
				foreach ($results as $result) {
					$json[] = array(
						'user_id' => $result['id'],
						'uname'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
						'contact'    => strip_tags(html_entity_decode($result['contact_no'], ENT_QUOTES, 'UTF-8')),
						'email'      => strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')),
						'utype'       => strip_tags(html_entity_decode($result['user_group_id'], ENT_QUOTES, 'UTF-8')),
					);
				}
				echo json_encode($json);
			}else
		echo 0;
		}else
		echo 0;
	}

}
