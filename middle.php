<?php
if(isset($_POST["page"])){
    $url = '';
    $post = array();

    switch($_POST["page"]) {
        case 0:
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/getGrade.php';
            $post = array("example"=>0);
            break;
        case 1:
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/getGrade.php';
            $post = array("example"=>0);
            break;
        case 2:
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/getGrade.php';
            $post = array("example"=>0);
            break;
        case 3:
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/getGrade.php';
            $post = array("example"=>0);
            break;
        case 4:
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/getGrade.php';
            $post = array("example"=>0);
            break;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
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
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else {
            error_log('Request failed: HTTP status code: ' . $resultStatus);
        }
    }
}

?>