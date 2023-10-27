<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loginapi extends MX_Controller {

    
    public function __construct()
    {
        parent::__construct();
        //Do your magic here
        $this->load->model("loginapi_model" , "loginapi");
    }
    

    public function index()
    {
        $output = array(
            "msg" => "Not allow",
        );
        $this->output->set_status_header(500);
        echo json_encode($output);
    }

    public function checkapi()
    {
        $this->loginapi->checkapi();
    }

}

/* End of file Controllername.php */



?>