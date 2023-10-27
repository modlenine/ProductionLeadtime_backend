<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loginapi_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
    }

    public function checkapi()
    {
        $output = array(
            "msg" => "allow",
        );
        echo json_encode($output);
    }
    
    

}

/* End of file ModelName.php */



?>