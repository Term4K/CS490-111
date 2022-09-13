<?php

$data = json_decode($_REQUEST["json"], true);
$data['page'] = "getTestCase";
error_log(json_encode($data));
$ch = curl_init('https://afsaccess4.njit.edu/~pn253/middle.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
error_log(json_encode($response));

if (curl_errno($ch)) {
    error_log('Couldn\'t send request: ' . curl_error($ch));
} else {
    // check the HTTP status code of the request
    $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($resultStatus == 200) {
        curl_close($ch);
    
        $data = json_decode($response, true);
        //error_log("JSON Response from Backend: " . json_encode($data));
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    } else {
        error_log('Request failed: HTTP status code: ' . $resultStatus);
    }
}

/* $grade3 = json_encode(array("funcName"=>-5, "testCase1"=>array("correct"=>0, "output"=>4), "testCase2"=>array("correct"=>0, "output"=>8), "totalGrade"=>"45/50"));
$grade5 = json_encode(array("funcName"=>-5, "testCase1"=>array("correct"=>0, "output"=>4), "testCase2"=>array("correct"=>0, "output"=>8), "totalGrade"=>"40/50"));
$data = array(3=>$grade3, 5=>$grade5);
echo json_encode($data); */
?>