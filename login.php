<?php
//rec POST request with user and pass set and send JSON back with login = true
if(isset($_POST["ucid"]) && isset($_POST["pass"])){
    error_log("login.php - Recieved: POST[user] => " . $_POST["ucid"] . "; POST[pass] => ****");
    
    $post = array("username"=>$_POST["ucid"], "passwd"=>$_POST["pass"]);
    $ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/backend_login.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Couldn\'t send request: ' . curl_error($ch));
    } else {
        // check the HTTP status code of the request
        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus == 200) {
            curl_close($ch);
    
            $data = json_decode($response, true);
            error_log("JSON Response from Backend: " . json_encode($data));
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else {
            error_log('Request failed: HTTP status code: ' . $resultStatus);
        }
    }

}

?>