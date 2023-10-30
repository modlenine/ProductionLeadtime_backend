<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mainapi extends MX_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model("mainapi_model" , "mainapi");
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
        $this->mainapi->checkapi();
    }

    public function checklogin()
    {
        $this->mainapi->checklogin();
    }

    public function checkdataworkplan()
    {
        $this->mainapi->getdataProdleadtime();
    }

    public function checkdataworkplan2()
    {
        $this->mainapi->checkdataworkplan2();
    }

    public function exportdata()
    {
        $this->mainapi->exportdata();
    }


    public function testcode()
    {

        // $startDatetime1 = "2023-09-11 22:05:00";
        // $finishDatetime1 = "2023-09-13 16:50:00";
        // echo getLeadtime($startDatetime1 , $finishDatetime1);

        // echo "<br>";

        // $startDatetime2 = "2023-09-15 21:40:00";
        // $finishDatetime2 = "2023-09-16 06:10:00";
        // echo getLeadtime($startDatetime2 , $finishDatetime2);

        // echo "<br>";
        
        // $startDatetime3 = "2023-09-16 23:05:00";
        // $finishDatetime3 = "2023-09-17 01:00:00";
        // echo getLeadtime($startDatetime3 , $finishDatetime3);.
        $this->mainapi->getTotalCount();
        
    }


    public function googlemap()
    {
        $this->load->view("index");
    }


    

}

/* End of file Controllername.php */

?>
