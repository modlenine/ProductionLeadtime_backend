<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mainapi_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        //Do your magic here
        date_default_timezone_set("Asia/Bangkok");
        $this->dbmix = $this->load->database("mix" , TRUE);
        $this->dbext = $this->load->database("ext" , TRUE);
        $this->dbsql = $this->load->database("mssql_prodplan" , true);
        $this->db2 = $this->load->database('saleecolour', TRUE);

    }

    public function checkapi()
    {
        $output = array(
            "msg" => "allow",
        );
        echo json_encode($output);
    }

    public function escape_string()
    {
        if($_SERVER['HTTP_HOST'] == "localhost"){
            return mysqli_connect("192.168.20.22", "ant", "Ant1234", "saleecolour");
        }else{
            return mysqli_connect("localhost", "ant", "Ant1234", "saleecolour");
        }

    }

    public function checklogin()
    {

        if ($this->input->post("username") != "" && $this->input->post("password") != "") {
            $username = $this->input->post("username");
            $password = $this->input->post("password");

            $user = mysqli_real_escape_string($this->escape_string(), $username);
            $pass = mysqli_real_escape_string($this->escape_string(), md5($password));

            // Check ว่าเป็นการ Login ของ Vender หรือว่า พนักงาน
            $sql = $this->db2->query(sprintf("SELECT * FROM member WHERE username='%s' AND password='%s' ", $user, $pass));
            if ($sql->num_rows() == 0) {
                $output = array(
                    "msg" => "ไม่พบข้อมูลผู้ใช้งานในระบบ",
                    "status" => "Login failed"
                );
            } else {
                $uri = isset($_SESSION['RedirectKe']) ? $_SESSION['RedirectKe'] : '/intsys/pdl/';
                // header('location:' . $uri);
                // Check IT
                $output = array(
                    "msg" => "ลงชื่อเข้าใช้สำเร็จ",
                    "status" => "Login Successfully",
                    "uri" => $uri,
                    "session_data" => $sql->row_array(),
                    "dateExpire" => strtotime(date("Y-m-d H:i:s")."+10 seconds"),
                );
            }


        }else{
            $output = array(
                "msg" => "กรุณากรอก Username & Password",
                "status" => "Login failed please fill username and password"
            );
        }
      
        echo json_encode($output);
    }


    // public function checkdataworkplan()
    // {

    //     $prodidArray = [];
    //     $dataareaidArray = [];
    //     $formno = [];
    //     $countGroup = [];
    //     $itemnoArray = [];
    //     $batchnoArray = [];
    //     $qtyschedArray = [];

    //     $queryProdtable = $this->queryProdtable(100);

    //     // Iterate over the results and add unique values to the merged array
    //     foreach ($queryProdtable->result() as $row) {
    //         $prodidArray[] = $row->prodid;
    //         $dataareaidArray[] = $row->dataareaid;
    //         $itemnoArray[] = $row->itemid;
    //         $batchnoArray[] = $row->inventbatchid;
    //         $qtyschedArray[] = $row->qtysched;
    //     }

    //     $testJobcardData = [];
    //     foreach ($prodidArray as $key => $row) {

    //         $areaid = $dataareaidArray[$key];
    //         $itemno = $itemnoArray[$key];
    //         $batchno = $batchnoArray[$key];
    //         $qtysched = $qtyschedArray[$key];

    //         $sqljobcard = $this->queryJobcard($row , $areaid);

    //         $sqlStartReceivedDoc = $this->queryStartReceivedDoc($row , $areaid);
    //         $journalid = $sqlStartReceivedDoc->row()->journalid;

    //         $sqlStartPrint = $this->queryStartPrint($journalid , $areaid);
    //         $sqlReserved = $this->queryReserved($journalid , $areaid);
    //         $sqlProcure = $this->queryProcurematerial($row , $areaid);
    //         $sqlProcuredone = $this->queryProcuredone($row , $areaid);


    //         //หาเวลาของการ Start ใบเบิก
    //         $calStartDoc = "";
    //         if($sqlStartPrint->row() !== null){
    //             $calStartDoc = strtotime(conDateTimeToDb($sqlStartPrint->row()->concreateddatetime));
    //         }else{
    //             $calStartDoc = "";
    //         }
    //         //หาเวลาของการ Start ใบเบิก

    //         //หาเวลาของการจอง Lot
    //         $calReserved = "";
    //         if($sqlReserved->row() !== null){
    //             $calReserved = strtotime(conDateTimeToDb($sqlReserved->row()->concreateddatetime));
    //         }else{
    //             $calReserved = "";
    //         }
    //         //หาเวลาของการจอง Lot

    //         //หาเวลาของการจัดเตรียมเสร็จ
    //         $calProcure = "";
    //         if($sqlProcure->row() !== null){
    //             $calProcure = strtotime(conDateTimeTodb($sqlProcure->row()->concreateddatetime));
    //         }else{
    //             $calProcure = "";
    //         }

    //         //หาเวลาของการจ่ายของ
    //         $calProcureDone = "";
    //         if($sqlProcuredone->row() !== null){
    //             $calProcureDone = strtotime(conDateTimeTodb($sqlProcuredone->row()->concreateddatetime));
    //         }else{
    //             $calProcureDone = "";
    //         }
    //         //หาเวลาของการจ่ายของ

    //         $leadtimeStartDocToReserved = "";
    //         $leadtimeReservedToProcure = "";
    //         $leadtimeProcureToProcuredone = "";

    //         if($calStartDoc != "" && $calReserved != ""){
    //             if($calStartDoc < $calReserved){
    //                 $leadtimeStartDocToReserved = $calReserved - $calStartDoc;
    //             }
    //         }

    //         if($calReserved != "" && $calProcure != ""){
    //             if($calReserved < $calProcure){
    //                 $leadtimeReservedToProcure = $calProcure - $calReserved;
    //             }
    //         }

    //         if($calProcure != "" && $calProcureDone != ""){
    //             if($calProcure < $calProcureDone){
    //                 $leadtimeProcureToProcuredone = $calProcureDone - $calProcure;
    //             }
    //         }

    //         $dataAll = [];

    //         //ข้อมูลชุดใหม่
    //         $leadtimeSumMix_seconds = 0;
    //         $leadtimeSumExt_seconds = 0;
    //         $leadtimeSumSep_seconds = 0;

    //         //ชุดข้อมูลที่เปลี่ยนวิธีการหาค่าใหม่
    //         $leadtimeMix_seconds = [];
    //         $leadtimeExt_seconds = [];
    //         $leadtimeSep_seconds = [];
            

    //         $dataGroupMix = [];
    //         $dataGroupExt = [];
    //         $dataGroupSep = [];

    //         $dataGroupMixTimedifference_seconds = [];
    //         $dataGroupExtTimedifference_seconds = [];
    //         $dataGroupSepTimedifference_seconds = [];

    //         $dataCurrent = [];
    //         $dataWaitUse = [];
    //         $dataTimedifference_seconds = [];
    //         $dataWaitTimedifference_seconds = [];

    //         // check data type
    //         $arrayDataCurrent = [];
    //         $arrayDataUse = [];

    //         $nextStationWaittimeMixAndExt = [];
    //         $nextStationWaittimeExtAndSep = [];

    //         $checkErrorProcessArray = [];
    //         $nextStationFail = false;
            
    //         foreach($sqljobcard->result() as $rs){
                
    //             $startDatetimeVar = $rs->TransDateFormTime;
    //             $endDatetimeVar = $rs->TransDateToTime;

    //             //คำนวณ Lead Time ของแต่ละ Station
    //             //ชุดข้อมูลใหม่
    //             $dataleadtime_time = $rs->timeDifference;
    //             $dataleadtime_seconds = $rs->timedifference_seconds;

    //             //คำนวณ Lead Time ของแต่ละ Station
    //             if($rs->OpeNum == '10'){
    //                 $leadtimeMix_seconds[] = $dataleadtime_seconds;
    //             }else if($rs->OpeNum == '20'){
    //                 $leadtimeExt_seconds[] = $dataleadtime_seconds;
    //             }else if($rs->OpeNum == '30'){
    //                 $leadtimeSep_seconds[] = $dataleadtime_seconds;
    //             }
    //             $leadtimeSumMix_seconds = array_sum($leadtimeMix_seconds);
    //             $leadtimeSumExt_seconds = array_sum($leadtimeExt_seconds);
    //             $leadtimeSumSep_seconds = array_sum($leadtimeSep_seconds);
    //             //คำนวณ Lead Time ของแต่ละ Station



    //             //คำนวณหาค่า Wait time ใน operation ตัวเอง
    //             if(empty($dataCurrent) || $rs->OpeNum === $dataCurrent[count($dataCurrent) - 1]){
    //                 $dataWaitCalc = array(
    //                     "openum" => $rs->OpeNum,
    //                     "fromtime" => $rs->TransDateFormTime,
    //                     "totime" => $rs->TransDateToTime,
    //                 );
    //                 $dataCurrent[] = $rs->OpeNum;
    //                 $dataWaitUse[] = $dataWaitCalc;
    //                 $dataWaitTimedifference_seconds[] = $rs->timedifference_seconds;
    //             }else if($rs->OpeNum !== $dataCurrent[count($dataCurrent) - 1]){
    //                 if(count($dataWaitUse) > 1){
    //                     if($dataCurrent[count($dataCurrent) - 1] == "10"){
    //                         $dataGroupMixTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
    //                         $dataGroupMix[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
    //                         $dataWaitTimedifference_seconds = [];
    //                     }else if($dataCurrent[count($dataCurrent) - 1] == "20"){
    //                         $dataGroupExt[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
    //                         $dataGroupExtTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
    //                     }else if($dataCurrent[count($dataCurrent) - 1] == "20"){
    //                         $dataGroupSep[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
    //                         $dataGroupSepTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
    //                     }
    //                 }
    //                 $dataCurrent = [];
    //                 $dataWaitUse = [];
    //                 $dataWaitTimedifference_seconds = [];
    //             }
    //             //คำนวณหาค่า Wait time ใน operation ตัวเอง
    //         }


    //         foreach($sqljobcard->result() as $rs){
    //             $mixFromtime = 0;
    //             $mixTotime = 0;
    //             $extFromtime = 0;
    //             $extTotime = 0;
    //             $sepFromtime = 0;
    //             $sepTotime = 0;

    //             // คำนวณหาค่า Wait time ของแต่ละ Station
    //             if(empty($arrayDataCurrent) || $rs->OpeNum === $arrayDataCurrent[count($arrayDataCurrent) - 1]){
    //                 $dataWaitCalc = array(
    //                     "openum" => $rs->OpeNum,
    //                     "fromtime" => $rs->TransDateFormTime,
    //                     "totime" => $rs->TransDateToTime
    //                 );
    //                 $arrayDataCurrent[] = $rs->OpeNum;
    //                 $arrayDataUse[] = $dataWaitCalc;
    //                 $checkErrorProcessArray[] = $rs->OpeNum;
    //             }else if($rs->OpeNum !== $arrayDataCurrent[count($arrayDataCurrent) - 1]){
    //                 // Check ข้อมูลว่าวิ่งตามที่ควรจะเป็นไหม
    //                 if(in_array($rs->OpeNum , $checkErrorProcessArray)){
    //                     if(strtotime($rs->TransDateFormTime) < strtotime($arrayDataUse[count($arrayDataUse)-1]['totime'])){
    //                         $nextStationFail = true;
    //                     }else{
    //                         if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
    //                             $mixTotime = end($arrayDataUse)['totime'];
    //                             $arrayDataUse = [];
    //                             $arrayDataCurrent = [];
    //                             $dataWaitCalc = array(
    //                                 "openum" => $rs->OpeNum,
    //                                 "fromtime" => $rs->TransDateFormTime,
    //                                 "totime" => $rs->TransDateToTime
    //                             );
    //                             $arrayDataUse[] = $dataWaitCalc;
    //                             $arrayDataCurrent[] = $rs->OpeNum;
    //                         }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
    //                             $extFromtime = $arrayDataUse[0]['fromtime'];
    //                             $extTotime = end($arrayDataUse)['totime'];
    //                             $arrayDataUse = [];
    //                             $arrayDataCurrent = [];
    //                             $dataWaitCalc = array(
    //                                 "openum" => $rs->OpeNum,
    //                                 "fromtime" => $rs->TransDateFormTime,
    //                                 "totime" => $rs->TransDateToTime
    //                             );
    //                             $arrayDataUse[] = $dataWaitCalc;
    //                             $arrayDataCurrent[] = $rs->OpeNum;
    //                         }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
    //                             $sepFromtime = $arrayDataUse[0]['fromtime'];
    //                             $sepTotime = end($arrayDataUse)['totime'];
    //                             $arrayDataUse = [];
    //                             $arrayDataCurrent = [];
    //                             $dataWaitCalc = array(
    //                                 "openum" => $rs->OpeNum,
    //                                 "fromtime" => $rs->TransDateFormTime,
    //                                 "totime" => $rs->TransDateToTime
    //                             );
    //                             $arrayDataUse[] = $dataWaitCalc;
    //                             $arrayDataCurrent[] = $rs->OpeNum;
    //                         }
    //                     }
    //                 }else{
    //                     if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
    //                         $mixTotime = end($arrayDataUse)['totime'];
    //                         $arrayDataUse = [];
    //                         $arrayDataCurrent = [];
    //                         $dataWaitCalc = array(
    //                             "openum" => $rs->OpeNum,
    //                             "fromtime" => $rs->TransDateFormTime,
    //                             "totime" => $rs->TransDateToTime
    //                         );
    //                         $arrayDataUse[] = $dataWaitCalc;
    //                         $arrayDataCurrent[] = $rs->OpeNum;
    //                     }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
    //                         $extFromtime = $arrayDataUse[0]['fromtime'];
    //                         $extTotime = end($arrayDataUse)['totime'];
    //                         $arrayDataUse = [];
    //                         $arrayDataCurrent = [];
    //                         $dataWaitCalc = array(
    //                             "openum" => $rs->OpeNum,
    //                             "fromtime" => $rs->TransDateFormTime,
    //                             "totime" => $rs->TransDateToTime
    //                         );
    //                         $arrayDataUse[] = $dataWaitCalc;
    //                         $arrayDataCurrent[] = $rs->OpeNum;
    //                     }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
    //                         $sepFromtime = $arrayDataUse[0]['fromtime'];
    //                         $sepTotime = end($arrayDataUse)['totime'];
    //                         $arrayDataUse = [];
    //                         $arrayDataCurrent = [];
    //                         $dataWaitCalc = array(
    //                             "openum" => $rs->OpeNum,
    //                             "fromtime" => $rs->TransDateFormTime,
    //                             "totime" => $rs->TransDateToTime
    //                         );
    //                         $arrayDataUse[] = $dataWaitCalc;
    //                         $arrayDataCurrent[] = $rs->OpeNum;
    //                     }
    //                 }
    //             }
                
    //             if($nextStationFail !== true){
    //                 if(count($arrayDataUse) > 0){
    //                     if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
    //                         $mixTotime = end($arrayDataUse)['totime'];
    //                     }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
    //                         $extFromtime = $arrayDataUse[0]['fromtime'];
    //                         $extTotime = end($arrayDataUse)['totime'];
    //                     }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
    //                         $sepFromtime = $arrayDataUse[0]['fromtime'];
    //                         $sepTotime = end($arrayDataUse)['totime'];
    //                     }
    //                 }

    //                 if($mixTotime !== 0 && $extFromtime !== 0){
    //                     if(strtotime($mixTotime) < strtotime($extFromtime)){
    //                         $nextStationWaittimeMixAndExt[] = strtotime($extFromtime) - strtotime($mixTotime);
    //                         $mixTotime = 0;
    //                         $extFromtime = 0;
    //                     }
    //                 }

    //                 if($extTotime !== 0 && $sepFromtime !== 0){
    //                     if(strtotime($extTotime) < strtotime($sepFromtime)){
    //                         $nextStationWaittimeExtAndSep[] = strtotime($sepFromtime) - strtotime($extTotime);
    //                     }
    //                 }

    //             }else{
    //                 $nextStationWaittimeMixAndExt = [];
    //                 $nextStationWaittimeExtAndSep = [];

    //                 $nextStationWaittimeMixAndExt[] = 999999;
    //                 $nextStationWaittimeExtAndSep[] = 999999;
    //             }
    //             // คำนวณหาค่า Wait time ของแต่ละ Station
    //         }

    //             //คำนวณหาค่า Wait time ใน operation ตัวเอง
    //             //mix
    //             $waittimeMixforcal = 0;
    //             if(!empty($dataGroupMix)){
    //                 $waittimeMixforcal = array_sum($dataGroupMix) - array_sum($dataGroupMixTimedifference_seconds);
    //             }
    //             //ext
    //             $waittimeExtforcal = 0;
    //             if(!empty($dataGroupExt)){
    //                 $waittimeExtforcal = array_sum($dataGroupExt) - array_sum($dataGroupExtTimedifference_seconds);
    //             }
    //             //sep
    //             $waittimeSepforcal = 0;
    //             if(!empty($dataGroupSep)){
    //                 $waittimeSepforcal = array_sum($dataGroupSep) - array_sum($dataGroupSepTimedifference_seconds);
    //             }
    //             //คำนวณหาค่า Wait time ใน operation ตัวเอง

    //             //Chcek ว่าเป็นงาน Special หรือว่า Adjust , Rerun
    //             $queryCheckAdjustRerun = $this->checkAdjustRerun($row);
    //             $adjustANDrerun = "";
    //             if(count($queryCheckAdjustRerun) > 1){
    //                 $adjustANDrerun = implode(', ', $queryCheckAdjustRerun);
    //             }else if(count($queryCheckAdjustRerun) == 1){
    //                 $adjustANDrerun = implode('', $queryCheckAdjustRerun);
    //                 if($nextStationFail == true){
    //                     $adjustANDrerun = "Speacial";
    //                 }
    //             }

    //             $waittimeToExt1 = "";
    //             $waittimeToExt2 = "";
    //             $waittimeToSep1 = "";
    //             $waittimeToSep2 = "";
    //             $resultSumDataMixExt = 0;
    //             $resultSumDataExtSep = 0;
    //             if(array_sum($nextStationWaittimeMixAndExt) == 999999){
    //                 $waittimeToExt1 = "Special";
    //                 $waittimeToExt2 = "Special";
    //             }else{
    //                 $resultSumDataMixExt = array_sum($nextStationWaittimeMixAndExt);
    //                 $waittimeToExt1 = conTime($resultSumDataMixExt);
    //                 $waittimeToExt2 = conTimeSecToDecimal($resultSumDataMixExt);
    //             }

    //             if(array_sum($nextStationWaittimeExtAndSep) == 999999){
    //                 $waittimeToSep1 = "Special";
    //                 $waittimeToSep2 = "Special";
    //             }else{
    //                 $resultSumDataExtSep = array_sum($nextStationWaittimeExtAndSep);
    //                 $waittimeToSep1 = conTime($resultSumDataExtSep);
    //                 $waittimeToSep2 = conTimeSecToDecimal($resultSumDataExtSep);
    //             }

            
    //         $dataAll = array(
    //             "Prodid" => $row,
    //             "dataAreaid" => $areaid,
    //             "itemno" => $itemno,
    //             "batchno" => $batchno,
    //             "qtysched" => number_format($qtysched , 3),
    //             "resultCheckAdjust" => $adjustANDrerun,

    //             "dataMixLeadtime" => conTimeSecToDecimal($leadtimeSumMix_seconds),
    //             "dataMixLeadtime2" => conTime($leadtimeSumMix_seconds),
    //             "dataMixWaitTime" => conTimeSecToDecimal($waittimeMixforcal),
    //             "dataMixWaitTime2" => conTime($waittimeMixforcal),

    //             "dataExtWait1" => $waittimeToExt2,
    //             "dataExtWait2" => $waittimeToExt1,

    //             "dataExtLeadtime" => conTimeSecToDecimal($leadtimeSumExt_seconds),
    //             "dataExtLeadtime2" => conTime($leadtimeSumExt_seconds),
    //             "dataExtWaitTime" => conTimeSecToDecimal($waittimeExtforcal),
    //             "dataExtWaitTime2" => conTime($waittimeExtforcal),

    //             "dataSepWait1" => $waittimeToSep1,
    //             "dataSepWait2" => $waittimeToSep2,

    //             "dataSepLeadtime" => conTimeSecToDecimal($leadtimeSumSep_seconds),
    //             "dataSepLeadtime2" => conTime($leadtimeSumSep_seconds),
    //             "dataSepWaitTime" => conTimeSecToDecimal($waittimeSepforcal),
    //             "dataSepWaitTime2" => conTime($waittimeSepforcal),

    //             "nextStationFail" => $nextStationFail,
    //             "startDocumentData" => $sqlStartReceivedDoc->row(),

    //             "startPrint" => $sqlStartPrint->row(),
    //             "reserved" => $sqlReserved->row(),
    //             "procure" => $sqlProcure->row(),
    //             "procuredone" => $sqlProcuredone->row(),

    //             "leadtimeStartDocToReserved" => conTime($leadtimeStartDocToReserved),
    //             "leadtimeStartDocToReservedDecimal" => conTimeSecToDecimal($leadtimeStartDocToReserved),
    //             "leadtimeReservedToProcure" => conTime($leadtimeReservedToProcure),
    //             "leadtimeReservedToProcureDecimal" => conTimeSecToDecimal($leadtimeReservedToProcure),
    //             "leadtimeProcureToProcuredone" => conTime($leadtimeProcureToProcuredone),
    //             "leadtimeProcureToProcuredoneDecimal" => conTimeSecToDecimal($leadtimeProcureToProcuredone),

    //             "checkadjustremixnormal" => $queryCheckAdjustRerun
    //         );


    //         if(count($sqljobcard->result()) !== 0){
    //             $formno[] = $dataAll;
    //             $testJobcardData[] = $sqljobcard->result();
    //         }

    //     }


    //     $output = array(
    //         "msg" => "ดึงข้อมูลสำเร็จแล้วนะ",
    //         "status" => "Select Data Success",
    //         // "result" => $mergedArray,
    //         "test" => $formno,
    //         // "test2" => $testJobcardData
    //     );

    //     echo json_encode($output);

    // }


    public function getdataProdleadtime()
    {
        $received_data = json_decode(file_get_contents("php://input"));
        $startdate = "";
        $enddate = "";
        $prodid = "";
        $batchno = "";
        $itemno = "";
        if($received_data->action == "getdata"){
            $page = $received_data->page;

            //filter input
            $startdate = $received_data->startdate;
            $enddate = $received_data->enddate;
            $prodid = $received_data->prodid;
            $batchno = $received_data->batchno;
            $itemno = $received_data->itemno;

            $pageSize = 5;

            $startIndex = ($page - 1) * $pageSize;
            // $endIndex = $startIndex + $itemsPerPage;

            $totalCount = $this->getTotalCount($startdate , $enddate , $prodid , $batchno , $itemno);

            $prodidArray = [];
            $dataareaidArray = [];
            $formno = [];
            $countGroup = [];
            $itemnoArray = [];
            $batchnoArray = [];
            $qtyschedArray = [];

            $queryProdtable = $this->queryProdtable($startIndex , $pageSize , $startdate , $enddate , $prodid , $batchno , $itemno);

            if($queryProdtable->num_rows() !== 0){

                foreach ($queryProdtable->result() as $row) {

                    $prodid = $row->prodid;
                    $areaid = $row->dataareaid;
                    $itemno = $row->itemid;
                    $batchno = $row->inventbatchid;
                    $qtysched = $row->qtysched;
                    $confirmshippingdate = $row->slc_shippingdateconfirmed;
    
                    $sqljobcard = $this->queryJobcard($prodid , $areaid);
    

                    $getprodleadtimedataSec1 = $this->getprodleadtimedataSec1($prodid , $areaid);
                    $startDocDatetime = "";
                    $reservedDocDatetime = "";
                    $procureDocDatetime = "";
                    $procureDoneDocDatetime = "";

                    //หาเวลาของการ Start ใบเบิก
                    $calStartDoc = "";
                    if($getprodleadtimedataSec1->row() !== null){
                        $calStartDoc = strtotime(conDateTimeToDb($getprodleadtimedataSec1->row()->startdoc));
                        $startDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->startdoc);
                    }else{
                        $calStartDoc = "";
                    }
                    //หาเวลาของการ Start ใบเบิก
    
                    //หาเวลาของการจอง Lot
                    $calReserved = "";
                    if($getprodleadtimedataSec1->row() !== null){
                        $calReserved = strtotime(conDateTimeToDb($getprodleadtimedataSec1->row()->reserved));
                        $reservedDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->reserved);
                    }else{
                        $calReserved = "";
                    }
                    //หาเวลาของการจอง Lot
    
                    //หาเวลาของการจัดเตรียมเสร็จ
                    $calProcure = "";
                    if($getprodleadtimedataSec1->row() !== null){
                        $calProcure = strtotime(conDateTimeTodb($getprodleadtimedataSec1->row()->procure));
                        $procureDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->procure);
                    }else{
                        $calProcure = "";
                    }
    
                    //หาเวลาของการจ่ายของ
                    $calProcureDone = "";
                    if($getprodleadtimedataSec1->row() !== null){
                        $calProcureDone = strtotime(conDateTimeTodb($getprodleadtimedataSec1->row()->procuredone));
                        $procureDoneDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->procuredone);
                    }else{
                        $calProcureDone = "";
                    }
                    //หาเวลาของการจ่ายของ
    
                    $leadtimeStartDocToReserved = "";
                    $leadtimeReservedToProcure = "";
                    $leadtimeProcureToProcuredone = "";
    
                    if($calStartDoc != "" && $calReserved != ""){
                        if($calStartDoc < $calReserved){
                            $leadtimeStartDocToReserved = $calReserved - $calStartDoc;
                        }
                    }
                    if($calReserved != "" && $calProcure != ""){
                        if($calReserved < $calProcure){
                            $leadtimeReservedToProcure = $calProcure - $calReserved;
                        }
                    }
                    if($calProcure != "" && $calProcureDone != ""){
                        if($calProcure < $calProcureDone){
                            $leadtimeProcureToProcuredone = $calProcureDone - $calProcure;
                        }
                    }
    
    
                    $dataAll = [];
    
                    //ข้อมูลชุดใหม่
                    $leadtimeSumMix_seconds = 0;
                    $leadtimeSumExt_seconds = 0;
                    $leadtimeSumSep_seconds = 0;
    
                    //ชุดข้อมูลที่เปลี่ยนวิธีการหาค่าใหม่
                    $leadtimeMix_seconds = [];
                    $leadtimeExt_seconds = [];
                    $leadtimeSep_seconds = [];
                    
    
                    $dataGroupMix = [];
                    $dataGroupExt = [];
                    $dataGroupSep = [];
    
                    $dataGroupMixTimedifference_seconds = [];
                    $dataGroupExtTimedifference_seconds = [];
                    $dataGroupSepTimedifference_seconds = [];
    
                    $dataCurrent = [];
                    $dataWaitUse = [];
                    $dataTimedifference_seconds = [];
                    $dataWaitTimedifference_seconds = [];
    
                    // check data type
                    $arrayDataCurrent = [];
                    $arrayDataUse = [];
    
                    $nextStationWaittimeMixAndExt = [];
                    $nextStationWaittimeExtAndSep = [];
    
                    $checkErrorProcessArray = [];
                    $nextStationFail = false;
    
                    $mixStartDate = "";
                    $mixStartDateCalc = "";
                    $extStartDate = "";
                    $sepStartDate = "";

                    // หาข้อมูลวันที่ผลิตเสร็จ
                    $production_completed_array = [];
    
                    foreach($sqljobcard->result() as $rs){
                        
                        $startDatetimeVar = $rs->TransDateFormTime;
                        $endDatetimeVar = $rs->TransDateToTime;
    
                        //คำนวณ Lead Time ของแต่ละ Station
                        //ชุดข้อมูลใหม่
                        $dataleadtime_time = $rs->timeDifference;
                        $dataleadtime_seconds = $rs->timedifference_seconds;
    
                        //คำนวณ Lead Time ของแต่ละ Station
                        if($rs->OpeNum == '10'){
                            $leadtimeMix_seconds[] = $dataleadtime_seconds;
                            $mixStartDate = conDateTimeFromDb($rs->TransDateFormTime);
                            $mixStartDateCalc = strtotime($rs->TransDateFormTime);
                        }else if($rs->OpeNum == '20'){
                            $leadtimeExt_seconds[] = $dataleadtime_seconds;
                            $extStartDate = conDateTimeFromDb($rs->TransDateFormTime);
                        }else if($rs->OpeNum == '30'){
                            $leadtimeSep_seconds[] = $dataleadtime_seconds;
                            $sepStartDate = conDateTimeFromDb($rs->TransDateFormTime);
                        }
                        $leadtimeSumMix_seconds = array_sum($leadtimeMix_seconds);
                        $leadtimeSumExt_seconds = array_sum($leadtimeExt_seconds);
                        $leadtimeSumSep_seconds = array_sum($leadtimeSep_seconds);
                        //คำนวณ Lead Time ของแต่ละ Station
    
    
    
                        //คำนวณหาค่า Wait time ใน operation ตัวเอง
                        if(empty($dataCurrent) || $rs->OpeNum === $dataCurrent[count($dataCurrent) - 1]){
                            $dataWaitCalc = array(
                                "openum" => $rs->OpeNum,
                                "fromtime" => $rs->TransDateFormTime,
                                "totime" => $rs->TransDateToTime,
                            );
                            $dataCurrent[] = $rs->OpeNum;
                            $dataWaitUse[] = $dataWaitCalc;
                            $dataWaitTimedifference_seconds[] = $rs->timedifference_seconds;
                        }else if($rs->OpeNum !== $dataCurrent[count($dataCurrent) - 1]){
                            if(count($dataWaitUse) > 1){
                                if($dataCurrent[count($dataCurrent) - 1] == "10"){
                                    $dataGroupMixTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
                                    $dataGroupMix[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
                                    $dataWaitTimedifference_seconds = [];
                                }else if($dataCurrent[count($dataCurrent) - 1] == "20"){
                                    $dataGroupExt[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
                                    $dataGroupExtTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
                                }else if($dataCurrent[count($dataCurrent) - 1] == "20"){
                                    $dataGroupSep[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
                                    $dataGroupSepTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
                                }
                            }
                            $dataCurrent = [];
                            $dataWaitUse = [];
                            $dataWaitTimedifference_seconds = [];
                        }
                        //คำนวณหาค่า Wait time ใน operation ตัวเอง
    
                        $mixFromtime = 0;
                        $mixTotime = 0;
                        $extFromtime = 0;
                        $extTotime = 0;
                        $sepFromtime = 0;
                        $sepTotime = 0;
    
                        // คำนวณหาค่า Wait time ของแต่ละ Station
                        if(empty($arrayDataCurrent) || $rs->OpeNum === $arrayDataCurrent[count($arrayDataCurrent) - 1]){
                            $dataWaitCalc = array(
                                "openum" => $rs->OpeNum,
                                "fromtime" => $rs->TransDateFormTime,
                                "totime" => $rs->TransDateToTime
                            );
                            $arrayDataCurrent[] = $rs->OpeNum;
                            $arrayDataUse[] = $dataWaitCalc;
                            $checkErrorProcessArray[] = $rs->OpeNum;
                        }else if($rs->OpeNum !== $arrayDataCurrent[count($arrayDataCurrent) - 1]){
                            // Check ข้อมูลว่าวิ่งตามที่ควรจะเป็นไหม
                            if(in_array($rs->OpeNum , $checkErrorProcessArray)){
                                if(strtotime($rs->TransDateFormTime) < strtotime($arrayDataUse[count($arrayDataUse)-1]['totime'])){
                                    $nextStationFail = true;
                                }else{
                                    if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
                                        $mixTotime = end($arrayDataUse)['totime'];
                                        $arrayDataUse = [];
                                        $arrayDataCurrent = [];
                                        $dataWaitCalc = array(
                                            "openum" => $rs->OpeNum,
                                            "fromtime" => $rs->TransDateFormTime,
                                            "totime" => $rs->TransDateToTime
                                        );
                                        $arrayDataUse[] = $dataWaitCalc;
                                        $arrayDataCurrent[] = $rs->OpeNum;
                                    }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
                                        $extFromtime = $arrayDataUse[0]['fromtime'];
                                        $extTotime = end($arrayDataUse)['totime'];
                                        $arrayDataUse = [];
                                        $arrayDataCurrent = [];
                                        $dataWaitCalc = array(
                                            "openum" => $rs->OpeNum,
                                            "fromtime" => $rs->TransDateFormTime,
                                            "totime" => $rs->TransDateToTime
                                        );
                                        $arrayDataUse[] = $dataWaitCalc;
                                        $arrayDataCurrent[] = $rs->OpeNum;
                                    }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
                                        $sepFromtime = $arrayDataUse[0]['fromtime'];
                                        $sepTotime = end($arrayDataUse)['totime'];
                                        $arrayDataUse = [];
                                        $arrayDataCurrent = [];
                                        $dataWaitCalc = array(
                                            "openum" => $rs->OpeNum,
                                            "fromtime" => $rs->TransDateFormTime,
                                            "totime" => $rs->TransDateToTime
                                        );
                                        $arrayDataUse[] = $dataWaitCalc;
                                        $arrayDataCurrent[] = $rs->OpeNum;
                                    }
                                }
                            }else{
                                if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
                                    $mixTotime = end($arrayDataUse)['totime'];
                                    $arrayDataUse = [];
                                    $arrayDataCurrent = [];
                                    $dataWaitCalc = array(
                                        "openum" => $rs->OpeNum,
                                        "fromtime" => $rs->TransDateFormTime,
                                        "totime" => $rs->TransDateToTime
                                    );
                                    $arrayDataUse[] = $dataWaitCalc;
                                    $arrayDataCurrent[] = $rs->OpeNum;
                                }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
                                    $extFromtime = $arrayDataUse[0]['fromtime'];
                                    $extTotime = end($arrayDataUse)['totime'];
                                    $arrayDataUse = [];
                                    $arrayDataCurrent = [];
                                    $dataWaitCalc = array(
                                        "openum" => $rs->OpeNum,
                                        "fromtime" => $rs->TransDateFormTime,
                                        "totime" => $rs->TransDateToTime
                                    );
                                    $arrayDataUse[] = $dataWaitCalc;
                                    $arrayDataCurrent[] = $rs->OpeNum;
                                }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
                                    $sepFromtime = $arrayDataUse[0]['fromtime'];
                                    $sepTotime = end($arrayDataUse)['totime'];
                                    $arrayDataUse = [];
                                    $arrayDataCurrent = [];
                                    $dataWaitCalc = array(
                                        "openum" => $rs->OpeNum,
                                        "fromtime" => $rs->TransDateFormTime,
                                        "totime" => $rs->TransDateToTime
                                    );
                                    $arrayDataUse[] = $dataWaitCalc;
                                    $arrayDataCurrent[] = $rs->OpeNum;
                                }
                            }
                        }
                        
                        if($nextStationFail !== true){
                            if(count($arrayDataUse) > 0){
                                if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
                                    $mixTotime = end($arrayDataUse)['totime'];
                                }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
                                    $extFromtime = $arrayDataUse[0]['fromtime'];
                                    $extTotime = end($arrayDataUse)['totime'];
                                }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
                                    $sepFromtime = $arrayDataUse[0]['fromtime'];
                                    $sepTotime = end($arrayDataUse)['totime'];
                                }
                            }
    
                            if($mixTotime !== 0 && $extFromtime !== 0){
                                if(strtotime($mixTotime) < strtotime($extFromtime)){
                                    $nextStationWaittimeMixAndExt[] = strtotime($extFromtime) - strtotime($mixTotime);
                                    $mixTotime = 0;
                                    $extFromtime = 0;
                                }
                            }
    
                            if($extTotime !== 0 && $sepFromtime !== 0){
                                if(strtotime($extTotime) < strtotime($sepFromtime)){
                                    $nextStationWaittimeExtAndSep[] = strtotime($sepFromtime) - strtotime($extTotime);
                                }
                            }
    
                        }else{
                            $nextStationWaittimeMixAndExt = [];
                            $nextStationWaittimeExtAndSep = [];
    
                            $nextStationWaittimeMixAndExt[] = 999999;
                            $nextStationWaittimeExtAndSep[] = 999999;
                        }
                        // คำนวณหาค่า Wait time ของแต่ละ Station

                        // หาข้อมูลวันเวลาที่ผลิตเสร็จ
                        $production_completed_array[] = $rs->TransDateToTime;
                    }
    
    
                    //คำนวณหาค่า Wait time ใน operation ตัวเอง
                    //mix
                    $waittimeMixforcal = 0;
                    if(!empty($dataGroupMix)){
                        $waittimeMixforcal = array_sum($dataGroupMix) - array_sum($dataGroupMixTimedifference_seconds);
                    }
                    //ext
                    $waittimeExtforcal = 0;
                    if(!empty($dataGroupExt)){
                        $waittimeExtforcal = array_sum($dataGroupExt) - array_sum($dataGroupExtTimedifference_seconds);
                    }
                    //sep
                    $waittimeSepforcal = 0;
                    if(!empty($dataGroupSep)){
                        $waittimeSepforcal = array_sum($dataGroupSep) - array_sum($dataGroupSepTimedifference_seconds);
                    }
                    //คำนวณหาค่า Wait time ใน operation ตัวเอง
    
                    //Chcek ว่าเป็นงาน Special หรือว่า Adjust , Rerun
                    $queryCheckAdjustRerun = $this->checkAdjustRerun($prodid);
                    $adjustANDrerun = "";
                    if(count($queryCheckAdjustRerun) > 1){
                        $adjustANDrerun = implode(', ', $queryCheckAdjustRerun);
                        if($nextStationFail == true){
                            $adjustANDrerun = "Special";
                        }
                    }else if(count($queryCheckAdjustRerun) == 1){
                        $adjustANDrerun = implode('', $queryCheckAdjustRerun);
                        if($nextStationFail == true){
                            $adjustANDrerun = "Special";
                        }
                    }
    
                    $waittimeToExt1 = "";
                    $waittimeToExt2 = "";
                    $waittimeToSep1 = "";
                    $waittimeToSep2 = "";
                    $resultSumDataMixExt = 0;
                    $resultSumDataExtSep = 0;
                    if(array_sum($nextStationWaittimeMixAndExt) == 999999){
                        $waittimeToExt1 = "Special";
                        $waittimeToExt2 = "Special";
                    }else{
                        $resultSumDataMixExt = array_sum($nextStationWaittimeMixAndExt);
                        $waittimeToExt1 = conTime($resultSumDataMixExt);
                        $waittimeToExt2 = conTimeSecToDecimal($resultSumDataMixExt);
                    }
    
                    if(array_sum($nextStationWaittimeExtAndSep) == 999999){
                        $waittimeToSep1 = "Special";
                        $waittimeToSep2 = "Special";
                    }else{
                        $resultSumDataExtSep = array_sum($nextStationWaittimeExtAndSep);
                        $waittimeToSep1 = conTime($resultSumDataExtSep);
                        $waittimeToSep2 = conTimeSecToDecimal($resultSumDataExtSep);
                    }
    

                    // หาชื่อเครื่องจักร
                    $queryWrkctrid_10 ="";
                    $queryWrkctrid_20 ="";
                    $queryWrkctrid_30 ="";
                    if($this->queryWrkctrid($prodid , $areaid , 10)->row() !== null){
                        $queryWrkctrid_10 = $this->queryWrkctrid($prodid , $areaid , 10)->row();
                        $queryWrkctrid_10 = $queryWrkctrid_10->wrkctrid;
                    }else{
                        $queryWrkctrid_10 ="";
                    }
    
                    if($this->queryWrkctrid($prodid , $areaid , 20)->row() !== null){
                        $queryWrkctrid_20 = $this->queryWrkctrid($prodid , $areaid , 20)->row();
                        $queryWrkctrid_20 = $queryWrkctrid_20->wrkctrid;
                    }else{
                        $queryWrkctrid_20 ="";
                    }
    
                    if($this->queryWrkctrid($prodid , $areaid , 30)->row() !== null){
                        $queryWrkctrid_30 = $this->queryWrkctrid($prodid , $areaid , 30)->row();
                        $queryWrkctrid_30 = $queryWrkctrid_30->wrkctrid;
                    }else{
                        $queryWrkctrid_30 ="";
                    }
                    // หาชื่อเครื่องจักร


                    //cal จัดเตรียม -> Mixer
                    $procureDoneDocTomixStartDate = "";
                    if($calProcureDone != "" && $mixStartDateCalc != ""){
                        if($mixStartDateCalc > $calProcureDone){
                            $procureDoneDocTomixStartDate = $mixStartDateCalc - $calProcureDone;
                        }
                    }
                    //cal จัดเตรียม -> Mixer


                    //หาค่าของการผลิตเสร็จ
                    $production_completedClac = "";
                    $production_completed = end($production_completed_array);
                    if($production_completed != ""){
                        $production_completedClac = strtotime($production_completed);
                        $production_completed = conDateTimeFromDb($production_completed);
                    }else{
                        $production_completedClac = "";
                    }
                    

                    //หาค่าของวันเวลา Post GR
                    $postGrCalc = "";
                    $postGrDatetime = "";
                    if($this->queryPostGr($prodid , $areaid)->row() !== null){
                        $postGrCalc = strtotime(conDateTimeToDb($this->queryPostGr($prodid , $areaid)->row()->posteddatetime));
                        $postGrDatetime = conDateTimeFromDb($this->queryPostGr($prodid , $areaid)->row()->posteddatetime);
                    }else{
                        $postGrCalc = "";
                        $postGrDatetime = "";
                    }

                    $procutionCompToGr = "";
                    if($postGrCalc > $production_completedClac && $production_completed !== false){
                        $procutionCompToGr = $postGrCalc - $production_completedClac;
                    }else{
                        $procutionCompToGr = "";
                    }

    
                    $dataAll = array(
                        "Prodid" => $prodid,
                        "dataAreaid" => $areaid,
                        "itemno" => $itemno,
                        "batchno" => $batchno,
                        "qtysched" => number_format($qtysched , 3),
                        "resultCheckAdjust" => $adjustANDrerun,
                        "wrkctrid_10" => $queryWrkctrid_10,
                        "wrkctrid_20" => $queryWrkctrid_20,
                        "wrkctrid_30" => $queryWrkctrid_30,

                        "mixStartDate" => $mixStartDate ,
                        "dataMixLeadtime" => conTimeSecToDecimal($leadtimeSumMix_seconds),
                        "dataMixLeadtime2" => conTime($leadtimeSumMix_seconds),
                        "dataMixWaitTime" => conTimeSecToDecimal($waittimeMixforcal),
                        "dataMixWaitTime2" => conTime($waittimeMixforcal),
    
                        "dataExtWait1" => $waittimeToExt2,
                        "dataExtWait2" => $waittimeToExt1,
    
                        "extStartDate" => $extStartDate ,
                        "dataExtLeadtime" => conTimeSecToDecimal($leadtimeSumExt_seconds),
                        "dataExtLeadtime2" => conTime($leadtimeSumExt_seconds),
                        "dataExtWaitTime" => conTimeSecToDecimal($waittimeExtforcal),
                        "dataExtWaitTime2" => conTime($waittimeExtforcal),
    
                        "dataSepWait1" => $waittimeToSep1,
                        "dataSepWait2" => $waittimeToSep2,
    
                        "sepStartDate" => $sepStartDate ,
                        "dataSepLeadtime" => conTimeSecToDecimal($leadtimeSumSep_seconds),
                        "dataSepLeadtime2" => conTime($leadtimeSumSep_seconds),
                        "dataSepWaitTime" => conTimeSecToDecimal($waittimeSepforcal),
                        "dataSepWaitTime2" => conTime($waittimeSepforcal),
    
                        "nextStationFail" => $nextStationFail,
                        // "startDocumentData" => $sqlStartReceivedDoc->row(),
    
                        // "startPrint" => $sqlStartPrint->row(),
                        // "reserved" => $sqlReserved->row(),
                        // "procure" => $sqlProcure->row(),
                        // "procuredone" => $sqlProcuredone->row(),
                        "startDocDateTime" => $startDocDatetime,
                        "reservedDocDateTime" => $reservedDocDatetime,
                        "procureDocDateTime" => $procureDocDatetime,
                        "procureDoneDocDateTime" => $procureDoneDocDatetime,
                        "leadtimeStartDocToReserved" => conTime($leadtimeStartDocToReserved),
                        "leadtimeStartDocToReservedDecimal" => conTimeSecToDecimal($leadtimeStartDocToReserved),
                        "leadtimeReservedToProcure" => conTime($leadtimeReservedToProcure),
                        "leadtimeReservedToProcureDecimal" => conTimeSecToDecimal($leadtimeReservedToProcure),
                        "leadtimeProcureToProcuredone" => conTime($leadtimeProcureToProcuredone),
                        "leadtimeProcureToProcuredoneDecimal" => conTimeSecToDecimal($leadtimeProcureToProcuredone),
    
                        "checkadjustremixnormal" => $queryCheckAdjustRerun,
                        "procureDoneDocTomixStartDate" => conTime($procureDoneDocTomixStartDate),
                        "procureDoneDocTomixStartDateDecimal" => conTimeSecToDecimal($procureDoneDocTomixStartDate),

                        "production_completedDate" => $production_completed,
                        "grDatetime" => "$postGrDatetime",
                        "productionCompToGr" => conTime($procutionCompToGr),
                        "productionCompToGrDecimal" => conTimeSecToDecimal($procutionCompToGr),
                        "confirmshippingdate" => conDateFromDb($confirmshippingdate),
                        "test" => $sqljobcard->num_rows()
                    );
    
                    // if(count($sqljobcard->result()) !== 0){
                    //     $formno[] = $dataAll;
                    // }
                    $formno[] = $dataAll;
    
                }// End Loop
    
                $output = array(
                    "msg" => "ดึงข้อมูลสำเร็จแล้วนะ",
                    "status" => "Select Data Success",
                    // "result" => $mergedArray,
                    "data" => $formno,
                    "total" => $totalCount,
                    "startindex" => $startIndex,
                    "pagesize" => $pageSize,
                    // "test2" => $testJobcardData
                );
            }else{
                $output = array(
                    "msg" => "ดึงข้อมูลสำเร็จแล้วนะ",
                    "status" => "Not Found Data",
                    // "result" => $mergedArray,
                    "data" => 0,
                    "total" => 0,
                    "startindex" => $startIndex,
                    "pagesize" => $pageSize,
                    "test2" => $queryProdtable->row(),
                    "prodid" => $prodid
                );
            }


        }else{
            $output = array(
                "msg" => "ดึงข้อมูลไม่สำเร็จ",
                "status" => "Select Data Not Success",
            );
        }
        echo json_encode($output);

    }


    private function queryProdtable($startIndex , $pageSize , $startdate , $enddate , $prodid , $batchno , $itemno)
    {
        $queryFilterDate = "";
        if($startdate != "" && $enddate != ""){
            $queryFilterDate = "AND prodtable.realdate between '$startdate' and '$enddate'";
        }else if($startdate != "" && $enddate == "" || $startdate == "" && $enddate != ""){
            $queryFilterDate = "AND prodtable.realdate = '$startdate'";
        }else{
            $queryFilterDate = "";
        }

        $queryFilterProdid = "";
        if($prodid != ""){
            $queryFilterProdid = "AND prodtable.prodid LIKE '%$prodid%'";
        }else{
            $queryFilterProdid = "";
        }

        $queryFilterBatchno = "";
        if($batchno != ""){
            $queryFilterBatchno = "AND inventdim.inventbatchid LIKE '%$batchno%'";
        }else{
            $queryFilterBatchno = "";
        }

        $queryFilterItemno = "";
        if($itemno != ""){
            $queryFilterItemno = "AND prodtable.itemid LIKE '%$itemno%'";
        }else{
            $queryFilterItemno = "";
        }


        $queryProdPlan = $this->dbsql->query("SELECT DISTINCT
        prodtable.dataareaid,
        prodtable.prodid,
        prodtable.inventdimid,
        prodtable.itemid,
        inventdim.inventbatchid,
        prodtable.qtysched,
        prodtable.slc_shippingdateconfirmed
        FROM
        prodtable 
        INNER JOIN inventdim ON inventdim.inventdimid = prodtable.inventdimid
        WHERE 
        prodtable.prodstatus IN (7) 
        AND inventdim.configid IN ('TWIN-L', 'TWIN-58')
        -- AND prodtable.finisheddate BETWEEN '2024-01-01' AND '2024-01-30' 
        $queryFilterDate $queryFilterProdid $queryFilterBatchno $queryFilterItemno
        AND prodtable.prodid IN (
            SELECT prodid
            FROM prodroute
            WHERE dataareaid = prodtable.dataareaid
                AND prodroute.prodid = prodtable.prodid
                AND prodroute.oprid NOT LIKE '%repac%'
        )

        order by prodtable.prodid desc
        OFFSET $startIndex ROWS
        FETCH NEXT $pageSize ROWS ONLY;
        ");

        return $queryProdPlan;
    }

    private function queryProdtable_export($startdate , $enddate , $prodid , $batchno , $itemno)
    {
        $queryFilterDate = "";
        if($startdate != "" && $enddate != ""){
            $queryFilterDate = "AND prodtable.realdate between '$startdate' and '$enddate'";
        }else if($startdate != "" && $enddate == "" || $startdate == "" && $enddate != ""){
            $queryFilterDate = "AND prodtable.realdate = '$startdate'";
        }else{
            $queryFilterDate = "";
        }

        $queryFilterProdid = "";
        if($prodid != ""){
            $queryFilterProdid = "AND prodtable.prodid LIKE '%$prodid%'";
        }else{
            $queryFilterProdid = "";
        }

        $queryFilterBatchno = "";
        if($batchno != ""){
            $queryFilterBatchno = "AND inventdim.inventbatchid LIKE '%$batchno%'";
        }else{
            $queryFilterBatchno = "";
        }

        $queryFilterItemno = "";
        if($itemno != ""){
            $queryFilterItemno = "AND prodtable.itemid LIKE '%$itemno%'";
        }else{
            $queryFilterItemno = "";
        }


        $queryProdPlan = $this->dbsql->query("SELECT DISTINCT
        prodtable.dataareaid,
        prodtable.prodid,
        prodtable.inventdimid,
        prodtable.itemid,
        inventdim.inventbatchid,
        prodtable.qtysched,
        prodtable.slc_shippingdateconfirmed,
        prodtable.realdate
        FROM
        prodtable 
        INNER JOIN inventdim ON inventdim.inventdimid = prodtable.inventdimid
        WHERE 
        prodtable.prodstatus IN (7) 
        AND inventdim.configid IN ('TWIN-L', 'TWIN-58')
        -- AND prodtable.finisheddate BETWEEN '2024-01-01' AND '2024-01-30' 
        $queryFilterDate $queryFilterProdid $queryFilterBatchno $queryFilterItemno
        AND prodtable.prodid IN (
            SELECT prodid
            FROM prodroute
            WHERE dataareaid = prodtable.dataareaid
                AND prodroute.prodid = prodtable.prodid
                AND prodroute.oprid NOT LIKE '%repac%'
        )
        order by prodtable.realdate desc
        ");

        return $queryProdPlan;
    }

    private function getTotalCount($startdate , $enddate , $prodid , $batchno , $itemno)
    {
        $queryFilterDate = "";
        if($startdate != "" && $enddate != ""){
            $queryFilterDate = "AND prodtable.realdate between '$startdate' and '$enddate'";
        }else if($startdate != "" && $enddate == "" || $startdate == "" && $enddate != ""){
            $queryFilterDate = "AND prodtable.realdate = '$startdate'";
        }else{
            $queryFilterDate = "";
        }

        $queryFilterProdid = "";
        if($prodid != ""){
            $queryFilterProdid = "AND prodtable.prodid LIKE '%$prodid%'";
        }else{
            $queryFilterProdid = "";
        }

        $queryFilterBatchno = "";
        if($batchno != ""){
            $queryFilterBatchno = "AND inventdim.inventbatchid LIKE '%$batchno%'";
        }else{
            $queryFilterBatchno = "";
        }

        $queryFilterItemno = "";
        if($itemno != ""){
            $queryFilterItemno = "AND prodtable.itemid LIKE '%$itemno%'";
        }else{
            $queryFilterItemno = "";
        }

        $queryGetTotalCount = $this->dbsql->query("SELECT 
        count(DISTINCT prodtable.prodid)AS total
        -- prodtable.prodid
        FROM prodtable
		inner join inventdim on inventdim.inventdimid = prodtable.inventdimid and inventdim.dataareaid = prodtable.dataareaid
        inner join prodroute on prodroute.prodid = prodtable.prodid and prodroute.dataareaid = prodtable.dataareaid
        where 
        prodtable.prodstatus in (7) 
        and inventdim.configid in ('TWIN-L' , 'TWIN-58')
        and prodroute.oprid NOT LIKE '%repac%' $queryFilterDate $queryFilterProdid $queryFilterBatchno $queryFilterItemno 
        -- AND prodtable.finisheddate between '2023-08-01' and '2023-10-09'
        -- and prodtable.prodid = 'PD66004080'
        ");
        return $queryGetTotalCount->row()->total;
    }

    private function queryJobcard($prodid , $areaid)
    {
        $queryjobcarddata = $this->dbext->query("SELECT
        ProdId,
        OpeNum,
        OpeId,
        JobId,
        TransDateFormTime,
        TransDateToTime,
        TIME_FORMAT(TIMEDIFF(TransDateToTime, TransDateFormTime), '%H:%i:%s') AS timeDifference,
        TIME_TO_SEC(TIMEDIFF(TransDateToTime, TransDateFormTime)) AS timedifference_seconds,
        Hours,
        DataArea
        FROM ProdJournalRoute
        WHERE ProdId = '$prodid' AND DataArea = '$areaid'
        ORDER BY TransDateFormTime ASC
        ");

        return $queryjobcarddata;
    }

    private function checkAdjustRerun($prodid)
    {
        if($prodid != ""){
            $sql = $this->dbmix->query("SELECT m_worktype 
            FROM main WHERE m_product_number = '$prodid' 
            GROUP BY m_worktype ORDER BY m_autoid
            ");
            $result = [];
            foreach($sql->result() as $rs){
                $result[] = $rs->m_worktype;
            }
            return $result;
        }
    }

    private function addUniqueValueToArray(&$array, $value) {
        if (!in_array($value, $array)) {
            $array[] = $value;
        }
    }

    private function checkLotinMixAndExt($resultArray)
    {
        $formno = array();
        foreach($resultArray as $key => $rs){
            array_push($formno , $rs->batch_number);
        }

        return $formno;
    }

    private function queryStartReceivedDoc($prodid , $areaid)
    {
        $sql = $this->dbsql->query("SELECT TOP 1 
        prodid , 
        journalid ,
        posteddatetime,
        SWITCHOFFSET(posteddatetime, '+07:00') AS datetimecon, 
        recid 
        FROM prodjournaltable 
        WHERE prodid = '$prodid' AND dataareaid = '$areaid'
        ORDER BY recid ASC");

        return $sql;
    }

    private function queryStartPrint($journalid , $areaid)
    {
        $sql = $this->dbsql->query("SELECT TOP 1
        journalid,
        printedby,
        createddatetime,
        SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime,
        dataareaid,
        recid 
        FROM slc_prodpickingprintedlog 
        WHERE journalid = '$journalid' AND dataareaid = '$areaid' 
        ORDER BY recid ASC");

        return $sql;
    }

    private function queryReserved($journalid , $areaid){
        $sql = $this->dbsql->query("SELECT 
        journalid , 
        receivedby , 
        slc_prodpickingaction , 
        remark , 
        createddatetime , 
        SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime,
        dataareaid , 
        recid 
        FROM slc_prodpickingreceivedlog 
        WHERE journalid = '$journalid' AND dataareaid = '$areaid'
        AND slc_prodpickingaction = 1");

        return $sql;
    }

    private function queryProcurematerial($prodid , $areaid){
        $sql = $this->dbsql->query("SELECT
        slc_whremark , 
        prodid , 
        createddatetime , 
        SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime,
        createdby , 
        dataareaid , 
        recid 
        FROM slc_prodwhremark 
        WHERE prodid = '$prodid' 
        AND slc_whremark LINK ('FRM จ่ายเสร็จแล้ว' , 'จ่ายเสร็จแล้ว') 
        AND dataareaid = '$areaid'
        ORDER BY recid ASC");

        return $sql;
    }

    private function queryProcuredone($prodid , $areaid){
        $sql = $this->dbsql->query("SELECT 
        slc_pdremark , 
        prodid , 
        SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime,
        createddatetime , 
        createdby , 
        dataareaid , 
        recid 
        FROM slc_prodpdremark 
        WHERE prodid = '$prodid' 
        -- AND slc_pdremark IN ('FPP จัดเตรียมเสร็จ' , 'จัดเตรียมเสร็จ')
        AND slc_pdremark LIKE '%FPP จัดเตรียมเสร็จ%'
        AND dataareaid = '$areaid'
        ORDER BY recid ASC");

        return $sql;
    }

    private function getprodleadtimedataSec1($prodid , $areaid)
    {
        $sql = $this->dbsql->query("SELECT TOP 1
            a.prodid as prodid,
            a.journalid as journalid,
            a.posteddatetime,
            a.recid,
            a.dataareaid,
            (
                SELECT TOP 1
                    SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime
                FROM
                    slc_prodpickingprintedlog
                WHERE
                    journalid = a.journalid AND dataareaid = a.dataareaid
                ORDER BY
                    recid ASC
            ) AS startdoc,
            (
                SELECT TOP 1 SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime
                FROM slc_prodpickingreceivedlog
                WHERE journalid = a.journalid AND dataareaid = a.dataareaid AND slc_prodpickingaction = 1
                ORDER BY recid ASC
            ) AS reserved,
            (
                SELECT TOP 1 SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime
                FROM slc_prodwhremark
                WHERE prodid = a.prodid 
                AND slc_whremark IN ('FRM จ่ายเสร็จแล้ว' , 'จ่ายเสร็จแล้ว') 
                AND dataareaid = a.dataareaid
                ORDER BY recid ASC
            ) AS procure,
            (
                SELECT TOP 1 SWITCHOFFSET(createddatetime, '+07:00') AS concreateddatetime
                FROM slc_prodpdremark
                WHERE prodid = a.prodid 
                AND slc_pdremark LIKE '%จัดเตรียมเสร็จ%'
                AND dataareaid = a.dataareaid
                ORDER BY recid ASC
            ) AS procuredone
        FROM
            prodjournaltable a
        WHERE
            a.prodid = '$prodid' AND a.dataareaid = '$areaid'
        ORDER BY
            recid ASC
        ");
        return $sql;
    }

    private function queryWrkctrid($prodid , $areaid , $oprnum)
    {
        if($prodid != "" && $areaid != ""){
            $sql = $this->dbsql->query("SELECT
            wrkctrid
            FROM prodroute
            WHERE prodid = '$prodid' AND dataareaid = '$areaid' AND oprnum = '$oprnum'
            ");

            return $sql;
        }
    }

    private function queryPostGr($prodid , $areaid)
    {
        $sql = $this->dbsql->query("SELECT TOP 1
        prodjournaltable.vcwmsstatus,
        SWITCHOFFSET(prodjournaltable.posteddatetime, '+07:00')AS posteddatetime
        FROM prodjournaltable 
        WHERE prodjournaltable.journalnameid = 'RASF' AND 
        prodjournaltable.prodid = '$prodid' AND dataareaid = '$areaid'
        ORDER BY prodjournaltable.posteddatetime ASC");
        return $sql;
    }

    public function exportdata()
    {
        if($this->input->post("startdate") != "" && $this->input->post("enddate") != ""){
            $startdate = $this->input->post("startdate");
            $enddate = $this->input->post("enddate");
            // $startdate = "2024-03-01";
            // $enddate = "2024-03-30";
            $prodid = $this->input->post("prodid");
            $batchno = $this->input->post("batchno");
            $itemno = $this->input->post("itemno");


            $prodidArray = [];
            $dataareaidArray = [];
            $dataResult = [];
            $countGroup = [];
            $itemnoArray = [];
            $batchnoArray = [];
            $qtyschedArray = [];

            $queryProdtable = $this->queryProdtable_export($startdate , $enddate , $prodid , $batchno , $itemno);

            foreach ($queryProdtable->result() as $row) {

                $prodid = $row->prodid;
                $areaid = $row->dataareaid;
                $itemno = $row->itemid;
                $batchno = $row->inventbatchid;
                $qtysched = $row->qtysched;
                $confirmshippingdate = $row->slc_shippingdateconfirmed;

                $sqljobcard = $this->queryJobcard($prodid , $areaid);


                $getprodleadtimedataSec1 = $this->getprodleadtimedataSec1($prodid , $areaid);
                $startDocDatetime = "";
                $reservedDocDatetime = "";
                $procureDocDatetime = "";
                $procureDoneDocDatetime = "";

                //หาเวลาของการ Start ใบเบิก
                $calStartDoc = "";
                if($getprodleadtimedataSec1->row() !== null){
                    $calStartDoc = strtotime(conDateTimeToDb($getprodleadtimedataSec1->row()->startdoc));
                    $startDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->startdoc);
                }else{
                    $calStartDoc = "";
                }
                //หาเวลาของการ Start ใบเบิก

                //หาเวลาของการจอง Lot
                $calReserved = "";
                if($getprodleadtimedataSec1->row() !== null){
                    $calReserved = strtotime(conDateTimeToDb($getprodleadtimedataSec1->row()->reserved));
                    $reservedDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->reserved);
                }else{
                    $calReserved = "";
                }
                //หาเวลาของการจอง Lot

                //หาเวลาของการจัดเตรียมเสร็จ
                $calProcure = "";
                if($getprodleadtimedataSec1->row() !== null){
                    $calProcure = strtotime(conDateTimeTodb($getprodleadtimedataSec1->row()->procure));
                    $procureDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->procure);
                }else{
                    $calProcure = "";
                }

                //หาเวลาของการจ่ายของ
                $calProcureDone = "";
                if($getprodleadtimedataSec1->row() !== null){
                    $calProcureDone = strtotime(conDateTimeTodb($getprodleadtimedataSec1->row()->procuredone));
                    $procureDoneDocDatetime = conDateTimeFromDb($getprodleadtimedataSec1->row()->procuredone);
                }else{
                    $calProcureDone = "";
                }
                //หาเวลาของการจ่ายของ

                $leadtimeStartDocToReserved = "";
                $leadtimeReservedToProcure = "";
                $leadtimeProcureToProcuredone = "";

                if($calStartDoc != "" && $calReserved != ""){
                    if($calStartDoc < $calReserved){
                        $leadtimeStartDocToReserved = $calReserved - $calStartDoc;
                    }
                }
                if($calReserved != "" && $calProcure != ""){
                    if($calReserved < $calProcure){
                        $leadtimeReservedToProcure = $calProcure - $calReserved;
                    }
                }
                if($calProcure != "" && $calProcureDone != ""){
                    if($calProcure < $calProcureDone){
                        $leadtimeProcureToProcuredone = $calProcureDone - $calProcure;
                    }
                }


                $dataAll = [];

                //ข้อมูลชุดใหม่
                $leadtimeSumMix_seconds = 0;
                $leadtimeSumExt_seconds = 0;
                $leadtimeSumSep_seconds = 0;

                //ชุดข้อมูลที่เปลี่ยนวิธีการหาค่าใหม่
                $leadtimeMix_seconds = [];
                $leadtimeExt_seconds = [];
                $leadtimeSep_seconds = [];
                

                $dataGroupMix = [];
                $dataGroupExt = [];
                $dataGroupSep = [];

                $dataGroupMixTimedifference_seconds = [];
                $dataGroupExtTimedifference_seconds = [];
                $dataGroupSepTimedifference_seconds = [];

                $dataCurrent = [];
                $dataWaitUse = [];
                $dataTimedifference_seconds = [];
                $dataWaitTimedifference_seconds = [];

                // check data type
                $arrayDataCurrent = [];
                $arrayDataUse = [];

                $nextStationWaittimeMixAndExt = [];
                $nextStationWaittimeExtAndSep = [];

                $checkErrorProcessArray = [];
                $nextStationFail = false;

                $mixStartDate = "";
                $mixStartDateCalc = "";
                $extStartDate = "";
                $sepStartDate = "";

                // หาข้อมูลวันที่ผลิตเสร็จ
                $production_completed_array = [];

                foreach($sqljobcard->result() as $rs){
                    
                    $startDatetimeVar = $rs->TransDateFormTime;
                    $endDatetimeVar = $rs->TransDateToTime;

                    //คำนวณ Lead Time ของแต่ละ Station
                    //ชุดข้อมูลใหม่
                    $dataleadtime_time = $rs->timeDifference;
                    $dataleadtime_seconds = $rs->timedifference_seconds;

                    //คำนวณ Lead Time ของแต่ละ Station
                    if($rs->OpeNum == '10'){
                        $leadtimeMix_seconds[] = $dataleadtime_seconds;
                        $mixStartDate = conDateTimeFromDb($rs->TransDateFormTime);
                        $mixStartDateCalc = strtotime($rs->TransDateFormTime);
                    }else if($rs->OpeNum == '20'){
                        $leadtimeExt_seconds[] = $dataleadtime_seconds;
                        $extStartDate = conDateTimeFromDb($rs->TransDateFormTime);
                    }else if($rs->OpeNum == '30'){
                        $leadtimeSep_seconds[] = $dataleadtime_seconds;
                        $sepStartDate = conDateTimeFromDb($rs->TransDateFormTime);
                    }
                    $leadtimeSumMix_seconds = array_sum($leadtimeMix_seconds);
                    $leadtimeSumExt_seconds = array_sum($leadtimeExt_seconds);
                    $leadtimeSumSep_seconds = array_sum($leadtimeSep_seconds);
                    //คำนวณ Lead Time ของแต่ละ Station



                    //คำนวณหาค่า Wait time ใน operation ตัวเอง
                    if(empty($dataCurrent) || $rs->OpeNum === $dataCurrent[count($dataCurrent) - 1]){
                        $dataWaitCalc = array(
                            "openum" => $rs->OpeNum,
                            "fromtime" => $rs->TransDateFormTime,
                            "totime" => $rs->TransDateToTime,
                        );
                        $dataCurrent[] = $rs->OpeNum;
                        $dataWaitUse[] = $dataWaitCalc;
                        $dataWaitTimedifference_seconds[] = $rs->timedifference_seconds;
                    }else if($rs->OpeNum !== $dataCurrent[count($dataCurrent) - 1]){
                        if(count($dataWaitUse) > 1){
                            if($dataCurrent[count($dataCurrent) - 1] == "10"){
                                $dataGroupMixTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
                                $dataGroupMix[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
                                $dataWaitTimedifference_seconds = [];
                            }else if($dataCurrent[count($dataCurrent) - 1] == "20"){
                                $dataGroupExt[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
                                $dataGroupExtTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
                            }else if($dataCurrent[count($dataCurrent) - 1] == "20"){
                                $dataGroupSep[] = strtotime(end($dataWaitUse)['totime']) - strtotime($dataWaitUse[0]['fromtime']);
                                $dataGroupSepTimedifference_seconds[] = array_sum($dataWaitTimedifference_seconds);
                            }
                        }
                        $dataCurrent = [];
                        $dataWaitUse = [];
                        $dataWaitTimedifference_seconds = [];
                    }
                    //คำนวณหาค่า Wait time ใน operation ตัวเอง

                    $mixFromtime = 0;
                    $mixTotime = 0;
                    $extFromtime = 0;
                    $extTotime = 0;
                    $sepFromtime = 0;
                    $sepTotime = 0;

                    // คำนวณหาค่า Wait time ของแต่ละ Station
                    if(empty($arrayDataCurrent) || $rs->OpeNum === $arrayDataCurrent[count($arrayDataCurrent) - 1]){
                        $dataWaitCalc = array(
                            "openum" => $rs->OpeNum,
                            "fromtime" => $rs->TransDateFormTime,
                            "totime" => $rs->TransDateToTime
                        );
                        $arrayDataCurrent[] = $rs->OpeNum;
                        $arrayDataUse[] = $dataWaitCalc;
                        $checkErrorProcessArray[] = $rs->OpeNum;
                    }else if($rs->OpeNum !== $arrayDataCurrent[count($arrayDataCurrent) - 1]){
                        // Check ข้อมูลว่าวิ่งตามที่ควรจะเป็นไหม
                        if(in_array($rs->OpeNum , $checkErrorProcessArray)){
                            if(strtotime($rs->TransDateFormTime) < strtotime($arrayDataUse[count($arrayDataUse)-1]['totime'])){
                                $nextStationFail = true;
                            }else{
                                if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
                                    $mixTotime = end($arrayDataUse)['totime'];
                                    $arrayDataUse = [];
                                    $arrayDataCurrent = [];
                                    $dataWaitCalc = array(
                                        "openum" => $rs->OpeNum,
                                        "fromtime" => $rs->TransDateFormTime,
                                        "totime" => $rs->TransDateToTime
                                    );
                                    $arrayDataUse[] = $dataWaitCalc;
                                    $arrayDataCurrent[] = $rs->OpeNum;
                                }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
                                    $extFromtime = $arrayDataUse[0]['fromtime'];
                                    $extTotime = end($arrayDataUse)['totime'];
                                    $arrayDataUse = [];
                                    $arrayDataCurrent = [];
                                    $dataWaitCalc = array(
                                        "openum" => $rs->OpeNum,
                                        "fromtime" => $rs->TransDateFormTime,
                                        "totime" => $rs->TransDateToTime
                                    );
                                    $arrayDataUse[] = $dataWaitCalc;
                                    $arrayDataCurrent[] = $rs->OpeNum;
                                }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
                                    $sepFromtime = $arrayDataUse[0]['fromtime'];
                                    $sepTotime = end($arrayDataUse)['totime'];
                                    $arrayDataUse = [];
                                    $arrayDataCurrent = [];
                                    $dataWaitCalc = array(
                                        "openum" => $rs->OpeNum,
                                        "fromtime" => $rs->TransDateFormTime,
                                        "totime" => $rs->TransDateToTime
                                    );
                                    $arrayDataUse[] = $dataWaitCalc;
                                    $arrayDataCurrent[] = $rs->OpeNum;
                                }
                            }
                        }else{
                            if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
                                $mixTotime = end($arrayDataUse)['totime'];
                                $arrayDataUse = [];
                                $arrayDataCurrent = [];
                                $dataWaitCalc = array(
                                    "openum" => $rs->OpeNum,
                                    "fromtime" => $rs->TransDateFormTime,
                                    "totime" => $rs->TransDateToTime
                                );
                                $arrayDataUse[] = $dataWaitCalc;
                                $arrayDataCurrent[] = $rs->OpeNum;
                            }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
                                $extFromtime = $arrayDataUse[0]['fromtime'];
                                $extTotime = end($arrayDataUse)['totime'];
                                $arrayDataUse = [];
                                $arrayDataCurrent = [];
                                $dataWaitCalc = array(
                                    "openum" => $rs->OpeNum,
                                    "fromtime" => $rs->TransDateFormTime,
                                    "totime" => $rs->TransDateToTime
                                );
                                $arrayDataUse[] = $dataWaitCalc;
                                $arrayDataCurrent[] = $rs->OpeNum;
                            }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
                                $sepFromtime = $arrayDataUse[0]['fromtime'];
                                $sepTotime = end($arrayDataUse)['totime'];
                                $arrayDataUse = [];
                                $arrayDataCurrent = [];
                                $dataWaitCalc = array(
                                    "openum" => $rs->OpeNum,
                                    "fromtime" => $rs->TransDateFormTime,
                                    "totime" => $rs->TransDateToTime
                                );
                                $arrayDataUse[] = $dataWaitCalc;
                                $arrayDataCurrent[] = $rs->OpeNum;
                            }
                        }
                    }
                    
                    if($nextStationFail !== true){
                        if(count($arrayDataUse) > 0){
                            if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "10"){
                                $mixTotime = end($arrayDataUse)['totime'];
                            }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "20"){
                                $extFromtime = $arrayDataUse[0]['fromtime'];
                                $extTotime = end($arrayDataUse)['totime'];
                            }else if($arrayDataUse[count($arrayDataUse)-1]['openum'] == "30"){
                                $sepFromtime = $arrayDataUse[0]['fromtime'];
                                $sepTotime = end($arrayDataUse)['totime'];
                            }
                        }

                        if($mixTotime !== 0 && $extFromtime !== 0){
                            if(strtotime($mixTotime) < strtotime($extFromtime)){
                                $nextStationWaittimeMixAndExt[] = strtotime($extFromtime) - strtotime($mixTotime);
                                $mixTotime = 0;
                                $extFromtime = 0;
                            }
                        }

                        if($extTotime !== 0 && $sepFromtime !== 0){
                            if(strtotime($extTotime) < strtotime($sepFromtime)){
                                $nextStationWaittimeExtAndSep[] = strtotime($sepFromtime) - strtotime($extTotime);
                            }
                        }

                    }else{
                        $nextStationWaittimeMixAndExt = [];
                        $nextStationWaittimeExtAndSep = [];

                        $nextStationWaittimeMixAndExt[] = 999999;
                        $nextStationWaittimeExtAndSep[] = 999999;
                    }
                    // คำนวณหาค่า Wait time ของแต่ละ Station

                    // หาข้อมูลวันเวลาที่ผลิตเสร็จ
                    $production_completed_array[] = $rs->TransDateToTime;
                }



                //คำนวณหาค่า Wait time ใน operation ตัวเอง
                //mix
                $waittimeMixforcal = 0;
                if(!empty($dataGroupMix)){
                    $waittimeMixforcal = array_sum($dataGroupMix) - array_sum($dataGroupMixTimedifference_seconds);
                }
                //ext
                $waittimeExtforcal = 0;
                if(!empty($dataGroupExt)){
                    $waittimeExtforcal = array_sum($dataGroupExt) - array_sum($dataGroupExtTimedifference_seconds);
                }
                //sep
                $waittimeSepforcal = 0;
                if(!empty($dataGroupSep)){
                    $waittimeSepforcal = array_sum($dataGroupSep) - array_sum($dataGroupSepTimedifference_seconds);
                }
                //คำนวณหาค่า Wait time ใน operation ตัวเอง

                //Chcek ว่าเป็นงาน Special หรือว่า Adjust , Rerun
                $queryCheckAdjustRerun = $this->checkAdjustRerun($prodid);
                $adjustANDrerun = "";
                if(count($queryCheckAdjustRerun) > 1){
                    $adjustANDrerun = implode(', ', $queryCheckAdjustRerun);
                    if($nextStationFail == true){
                        $adjustANDrerun = "Special";
                    }
                }else if(count($queryCheckAdjustRerun) == 1){
                    $adjustANDrerun = implode('', $queryCheckAdjustRerun);
                    if($nextStationFail == true){
                        $adjustANDrerun = "Special";
                    }
                }

                $waittimeToExt1 = "";
                $waittimeToExt2 = "";
                $waittimeToSep1 = "";
                $waittimeToSep2 = "";
                $resultSumDataMixExt = 0;
                $resultSumDataExtSep = 0;
                if(array_sum($nextStationWaittimeMixAndExt) == 999999){
                    $waittimeToExt1 = "Special";
                    $waittimeToExt2 = "Special";
                }else{
                    $resultSumDataMixExt = array_sum($nextStationWaittimeMixAndExt);
                    $waittimeToExt1 = conTime($resultSumDataMixExt);
                    $waittimeToExt2 = conTimeSecToDecimal($resultSumDataMixExt);
                }

                if(array_sum($nextStationWaittimeExtAndSep) == 999999){
                    $waittimeToSep1 = "Special";
                    $waittimeToSep2 = "Special";
                }else{
                    $resultSumDataExtSep = array_sum($nextStationWaittimeExtAndSep);
                    $waittimeToSep1 = conTime($resultSumDataExtSep);
                    $waittimeToSep2 = conTimeSecToDecimal($resultSumDataExtSep);
                }



                $queryWrkctrid_10 ="";
                $queryWrkctrid_20 ="";
                $queryWrkctrid_30 ="";
                if($this->queryWrkctrid($prodid , $areaid , 10)->row() !== null){
                    $queryWrkctrid_10 = $this->queryWrkctrid($prodid , $areaid , 10)->row();
                    $queryWrkctrid_10 = $queryWrkctrid_10->wrkctrid;
                }else{
                    $queryWrkctrid_10 ="";
                }

                if($this->queryWrkctrid($prodid , $areaid , 20)->row() !== null){
                    $queryWrkctrid_20 = $this->queryWrkctrid($prodid , $areaid , 20)->row();
                    $queryWrkctrid_20 = $queryWrkctrid_20->wrkctrid;
                }else{
                    $queryWrkctrid_20 ="";
                }

                if($this->queryWrkctrid($prodid , $areaid , 30)->row() !== null){
                    $queryWrkctrid_30 = $this->queryWrkctrid($prodid , $areaid , 30)->row();
                    $queryWrkctrid_30 = $queryWrkctrid_30->wrkctrid;
                }else{
                    $queryWrkctrid_30 ="";
                }

                //cal จัดเตรียม -> Mixer
                $procureDoneDocTomixStartDate = "";
                if($calProcureDone != "" && $mixStartDateCalc != ""){
                    if($mixStartDateCalc > $calProcureDone){
                        $procureDoneDocTomixStartDate = $mixStartDateCalc - $calProcureDone;
                    }
                }


                //หาค่าของการผลิตเสร็จ
                $production_completedClac = "";
                $production_completed = end($production_completed_array);
                if($production_completed != ""){
                    $production_completedClac = strtotime($production_completed);
                    $production_completed = conDateTimeFromDb($production_completed);
                }else{
                    $production_completedClac = 0;
                }

                //หาค่าของวันเวลา Post GR
                $postGrCalc = "";
                $postGrDatetime = "";
                if($this->queryPostGr($prodid , $areaid)->row() !== null){
                    $postGrCalc = strtotime(conDateTimeToDb($this->queryPostGr($prodid , $areaid)->row()->posteddatetime));
                    $postGrDatetime = conDateTimeFromDb($this->queryPostGr($prodid , $areaid)->row()->posteddatetime);
                }else{
                    $postGrCalc = 0;
                    $postGrDatetime = "";
                }

                $procutionCompToGr = "";
                if($postGrCalc > $production_completedClac){
                    $procutionCompToGr = $postGrCalc - $production_completedClac;
                }


                $dataAll = array(
                    "Prodid" => $prodid,
                    "dataAreaid" => $areaid,
                    "itemno" => $itemno,
                    "batchno" => $batchno,
                    "qtysched" => number_format($qtysched , 3),
                    "resultCheckAdjust" => $adjustANDrerun,
                    "wrkctrid_10" => $queryWrkctrid_10,
                    "wrkctrid_20" => $queryWrkctrid_20,
                    "wrkctrid_30" => $queryWrkctrid_30,

                    "mixStartDate" => $mixStartDate ,
                    "dataMixLeadtime" => conTimeSecToDecimal($leadtimeSumMix_seconds),
                    "dataMixLeadtime2" => conTime($leadtimeSumMix_seconds),
                    "dataMixWaitTime" => conTimeSecToDecimal($waittimeMixforcal),
                    "dataMixWaitTime2" => conTime($waittimeMixforcal),

                    "dataExtWait1" => $waittimeToExt2,
                    "dataExtWait2" => $waittimeToExt1,

                    "extStartDate" => $extStartDate ,
                    "dataExtLeadtime" => conTimeSecToDecimal($leadtimeSumExt_seconds),
                    "dataExtLeadtime2" => conTime($leadtimeSumExt_seconds),
                    "dataExtWaitTime" => conTimeSecToDecimal($waittimeExtforcal),
                    "dataExtWaitTime2" => conTime($waittimeExtforcal),

                    "dataSepWait1" => $waittimeToSep1,
                    "dataSepWait2" => $waittimeToSep2,

                    "sepStartDate" => $sepStartDate ,
                    "dataSepLeadtime" => conTimeSecToDecimal($leadtimeSumSep_seconds),
                    "dataSepLeadtime2" => conTime($leadtimeSumSep_seconds),
                    "dataSepWaitTime" => conTimeSecToDecimal($waittimeSepforcal),
                    "dataSepWaitTime2" => conTime($waittimeSepforcal),

                    "nextStationFail" => $nextStationFail,
                    // "startDocumentData" => $sqlStartReceivedDoc->row(),

                    // "startPrint" => $sqlStartPrint->row(),
                    // "reserved" => $sqlReserved->row(),
                    // "procure" => $sqlProcure->row(),
                    // "procuredone" => $sqlProcuredone->row(),
                    "startDocDateTime" => $startDocDatetime,
                    "reservedDocDateTime" => $reservedDocDatetime,
                    "procureDocDateTime" => $procureDocDatetime,
                    "procureDoneDocDateTime" => $procureDoneDocDatetime,
                    "leadtimeStartDocToReserved" => conTime($leadtimeStartDocToReserved),
                    "leadtimeStartDocToReservedDecimal" => conTimeSecToDecimal($leadtimeStartDocToReserved),
                    "leadtimeReservedToProcure" => conTime($leadtimeReservedToProcure),
                    "leadtimeReservedToProcureDecimal" => conTimeSecToDecimal($leadtimeReservedToProcure),
                    "leadtimeProcureToProcuredone" => conTime($leadtimeProcureToProcuredone),
                    "leadtimeProcureToProcuredoneDecimal" => conTimeSecToDecimal($leadtimeProcureToProcuredone),

                    "checkadjustremixnormal" => $queryCheckAdjustRerun,
                    "procureDoneDocTomixStartDate" => conTime($procureDoneDocTomixStartDate),
                    "procureDoneDocTomixStartDateDecimal" => conTimeSecToDecimal($procureDoneDocTomixStartDate),


                    "production_completedDate" => $production_completed,
                    "grDatetime" => "$postGrDatetime",
                    "productionCompToGr" => conTime($procutionCompToGr),
                    "productionCompToGrDecimal" => conTimeSecToDecimal($procutionCompToGr),
                    "confirmshippingdate" => conDateFromDb($confirmshippingdate),
                );

                $dataResult[] = $dataAll;

            }// End Loop

            // $output = array(
            //     "result" => $dataResult
            // );






            //Create Excel file

            require("PHPExcel/Classes/PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            //กำหนดส่วนหัวเป็น Column แบบ Fix ไม่มีการเปลี่ยนแปลงใดๆ
    
            $objPHPExcel->getActiveSheet()->setCellValue('a1', 'PD');
            $objPHPExcel->getActiveSheet()->setCellValue('b1', 'Mix');
            $objPHPExcel->getActiveSheet()->setCellValue('c1', 'Extrude');
            $objPHPExcel->getActiveSheet()->setCellValue('d1', 'Separate');
            $objPHPExcel->getActiveSheet()->setCellValue('e1', 'Company');
            $objPHPExcel->getActiveSheet()->setCellValue('f1', 'Work type');
            $objPHPExcel->getActiveSheet()->setCellValue('g1', 'Item No');
            $objPHPExcel->getActiveSheet()->setCellValue('h1', 'Batch No');
            $objPHPExcel->getActiveSheet()->setCellValue('i1', 'QTY');
            $objPHPExcel->getActiveSheet()->setCellValue('j1', 'Startใบเบิก');
            $objPHPExcel->getActiveSheet()->setCellValue('k1', 'Startใบเบิก -> จองLot');
            $objPHPExcel->getActiveSheet()->setCellValue('l1', 'Startใบเบิก -> จองLot');
            $objPHPExcel->getActiveSheet()->setCellValue('m1', 'จองLot');
            $objPHPExcel->getActiveSheet()->setCellValue('n1', 'จองLot -> จ่ายของเสร็จ');
            $objPHPExcel->getActiveSheet()->setCellValue('o1', 'จองLot -> จ่ายของเสร็จ');
            $objPHPExcel->getActiveSheet()->setCellValue('p1', 'จ่ายของเสร็จ');
            $objPHPExcel->getActiveSheet()->setCellValue('q1', 'จ่ายของเสร็จ -> จัดเตรียมเสร็จ');
            $objPHPExcel->getActiveSheet()->setCellValue('r1', 'จ่ายของเสร็จ -> จัดเตรียมเสร็จ');
            $objPHPExcel->getActiveSheet()->setCellValue('s1', 'จัดเตรียมเสร็จ');
            $objPHPExcel->getActiveSheet()->setCellValue('t1', 'จัดเตรียมเสร็จ -> Mixer');
            $objPHPExcel->getActiveSheet()->setCellValue('u1', 'จัดเตรียมเสร็จ -> Mixer');
            $objPHPExcel->getActiveSheet()->setCellValue('v1', 'Mixer');
            $objPHPExcel->getActiveSheet()->setCellValue('w1', 'Mix Leadtime');
            $objPHPExcel->getActiveSheet()->setCellValue('x1', 'Mix Leadtime');
            $objPHPExcel->getActiveSheet()->setCellValue('y1', 'Mix Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('z1', 'Mix Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('aa1', 'Next Station Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('ab1', 'Next Station Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('ac1', 'Extrude');
            $objPHPExcel->getActiveSheet()->setCellValue('ad1', 'Ext Leadtime');
            $objPHPExcel->getActiveSheet()->setCellValue('ae1', 'Ext Leadtime');
            $objPHPExcel->getActiveSheet()->setCellValue('af1', 'Ext Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('ag1', 'Ext Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('ah1', 'Next Station Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('ai1', 'Next Station Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('aj1', 'Separate');
            $objPHPExcel->getActiveSheet()->setCellValue('ak1', 'Sep Leadtime');
            $objPHPExcel->getActiveSheet()->setCellValue('al1', 'Sep Leadtime');
            $objPHPExcel->getActiveSheet()->setCellValue('am1', 'Sep Waittime');
            $objPHPExcel->getActiveSheet()->setCellValue('an1', 'Sep Waittime');

            $objPHPExcel->getActiveSheet()->setCellValue('ao1', 'ผลิตเสร็จ');
            $objPHPExcel->getActiveSheet()->setCellValue('ap1', 'ผลิตเสร็จ -> ออกGR');
            $objPHPExcel->getActiveSheet()->setCellValue('aq1', 'ผลิตเสร็จ -> ออกGR');
            $objPHPExcel->getActiveSheet()->setCellValue('ar1', 'ออกGR');
            $objPHPExcel->getActiveSheet()->setCellValue('as1', 'Confirm Ship Date');
    
            // Loop Time
            $t1 = 2;
            for($ii = 0; $ii < count($dataResult); $ii++){
                
                $objPHPExcel->getActiveSheet()->setCellValue('a'.$t1 , $dataResult[$ii]['Prodid']);
                $objPHPExcel->getActiveSheet()->setCellValue('b'.$t1 , $dataResult[$ii]['wrkctrid_10']);
                $objPHPExcel->getActiveSheet()->setCellValue('c'.$t1 , $dataResult[$ii]['wrkctrid_20']);
                $objPHPExcel->getActiveSheet()->setCellValue('d'.$t1 , $dataResult[$ii]['wrkctrid_30']);
                $objPHPExcel->getActiveSheet()->setCellValue('e'.$t1 , $dataResult[$ii]['dataAreaid']);
                $objPHPExcel->getActiveSheet()->setCellValue('f'.$t1 , $dataResult[$ii]['resultCheckAdjust']);
                $objPHPExcel->getActiveSheet()->setCellValue('g'.$t1 , $dataResult[$ii]['itemno']);
                $objPHPExcel->getActiveSheet()->setCellValue('h'.$t1 , $dataResult[$ii]['batchno']);
                $objPHPExcel->getActiveSheet()->setCellValue('i'.$t1 , $dataResult[$ii]['qtysched']);
                $objPHPExcel->getActiveSheet()->setCellValue('j'.$t1 , $dataResult[$ii]['startDocDateTime']);
                $objPHPExcel->getActiveSheet()->setCellValue('k'.$t1 , $dataResult[$ii]['leadtimeStartDocToReservedDecimal']);
                $objPHPExcel->getActiveSheet()->setCellValue('l'.$t1 , $dataResult[$ii]['leadtimeStartDocToReserved']);
                $objPHPExcel->getActiveSheet()->setCellValue('m'.$t1 , $dataResult[$ii]['reservedDocDateTime']);
                $objPHPExcel->getActiveSheet()->setCellValue('n'.$t1 , $dataResult[$ii]['leadtimeReservedToProcureDecimal']);
                $objPHPExcel->getActiveSheet()->setCellValue('o'.$t1 , $dataResult[$ii]['leadtimeReservedToProcure']);
                $objPHPExcel->getActiveSheet()->setCellValue('p'.$t1 , $dataResult[$ii]['procureDocDateTime']);
                $objPHPExcel->getActiveSheet()->setCellValue('q'.$t1 , $dataResult[$ii]['leadtimeProcureToProcuredoneDecimal']);
                $objPHPExcel->getActiveSheet()->setCellValue('r'.$t1 , $dataResult[$ii]['leadtimeProcureToProcuredone']);
                $objPHPExcel->getActiveSheet()->setCellValue('s'.$t1 , $dataResult[$ii]['procureDoneDocDateTime']);
                $objPHPExcel->getActiveSheet()->setCellValue('t'.$t1 , $dataResult[$ii]['procureDoneDocTomixStartDateDecimal']);
                $objPHPExcel->getActiveSheet()->setCellValue('u'.$t1 , $dataResult[$ii]['procureDoneDocTomixStartDate']);
                $objPHPExcel->getActiveSheet()->setCellValue('v'.$t1 , $dataResult[$ii]['mixStartDate']);
                $objPHPExcel->getActiveSheet()->setCellValue('w'.$t1 , $dataResult[$ii]['dataMixLeadtime']);
                $objPHPExcel->getActiveSheet()->setCellValue('x'.$t1 , $dataResult[$ii]['dataMixLeadtime2']);
                $objPHPExcel->getActiveSheet()->setCellValue('y'.$t1 , $dataResult[$ii]['dataMixWaitTime']);
                $objPHPExcel->getActiveSheet()->setCellValue('z'.$t1 , $dataResult[$ii]['dataMixWaitTime2']);
                $objPHPExcel->getActiveSheet()->setCellValue('aa'.$t1 , $dataResult[$ii]['dataExtWait1']);
                $objPHPExcel->getActiveSheet()->setCellValue('ab'.$t1 , $dataResult[$ii]['dataExtWait2']);
                $objPHPExcel->getActiveSheet()->setCellValue('ac'.$t1 , $dataResult[$ii]['extStartDate']);
                $objPHPExcel->getActiveSheet()->setCellValue('ad'.$t1 , $dataResult[$ii]['dataExtLeadtime']);
                $objPHPExcel->getActiveSheet()->setCellValue('ae'.$t1 , $dataResult[$ii]['dataExtLeadtime2']);
                $objPHPExcel->getActiveSheet()->setCellValue('af'.$t1 , $dataResult[$ii]['dataExtWaitTime']);
                $objPHPExcel->getActiveSheet()->setCellValue('ag'.$t1 , $dataResult[$ii]['dataExtWaitTime2']);
                $objPHPExcel->getActiveSheet()->setCellValue('ah'.$t1 , $dataResult[$ii]['dataSepWait1']);
                $objPHPExcel->getActiveSheet()->setCellValue('ai'.$t1 , $dataResult[$ii]['dataSepWait2']);
                $objPHPExcel->getActiveSheet()->setCellValue('aj'.$t1 , $dataResult[$ii]['sepStartDate']);
                $objPHPExcel->getActiveSheet()->setCellValue('ak'.$t1 , $dataResult[$ii]['dataSepLeadtime']);
                $objPHPExcel->getActiveSheet()->setCellValue('al'.$t1 , $dataResult[$ii]['dataSepLeadtime2']);
                $objPHPExcel->getActiveSheet()->setCellValue('am'.$t1 , $dataResult[$ii]['dataSepWaitTime']);
                $objPHPExcel->getActiveSheet()->setCellValue('an'.$t1 , $dataResult[$ii]['dataSepWaitTime2']);

                $objPHPExcel->getActiveSheet()->setCellValue('ao'.$t1 , $dataResult[$ii]['production_completedDate']);
                $objPHPExcel->getActiveSheet()->setCellValue('ap'.$t1 , $dataResult[$ii]['productionCompToGrDecimal']);
                $objPHPExcel->getActiveSheet()->setCellValue('aq'.$t1 , $dataResult[$ii]['productionCompToGr']);
                $objPHPExcel->getActiveSheet()->setCellValue('ar'.$t1 , $dataResult[$ii]['grDatetime']);
                $objPHPExcel->getActiveSheet()->setCellValue('as'.$t1 , $dataResult[$ii]['confirmshippingdate']);
    
                $t1++;
            }
            // Loop Time

            $dateNow = date("Y-m-d H:i:s");
            $contoTime = strtotime($dateNow);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="รายงาน Production leadtime-'.$contoTime.'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);
        }
        // else{
        //     $output = array(
        //         "result" => null
        //     );
        // }

        // echo json_encode($output);

    }


    

}

/* End of file Mainapi_model.php */

?>