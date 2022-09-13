<?php

if(isset($_POST['page'])){
    $url = "";
    switch($_POST['page']){
        case "makeExam":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_exams.php';
            break;
        case "listExams":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_exams.php';
            break;
        case "examQuestions":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_exams.php';
            break;
        case "submitExam":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_exams.php';
            break;
        case "teacherScore":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_grades.php';
            break;
        case "getTestCase":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_grades.php';
            break;
        case "updateGrade":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_grades.php';
            break;
        case "getStudentScore":
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_grades.php';
            break;
        default:
            $url = 'https://afsaccess4.njit.edu/~as3526/CS490/backend_beta.php';
    }
    error_log("JSON of POST middle.php to url ".$url.": " . json_encode($_POST));
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
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
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else {
            error_log('Request failed: HTTP status code: ' . $resultStatus);
        }
    }
}

?>