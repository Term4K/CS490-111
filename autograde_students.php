<?php 
//functions to help autograde

/* //Testing priv for running and writing files afs 
$runFile = fopen("resTest.py", "w");
fwrite($runFile, "print('hello world')");
$command = "/afs/cad/linux/anaconda3.8/anaconda/bin/python3 2>&1 resTest.py";
$output = shell_exec($command);
if(strpos($output, "resTest.py") === false){
    echo $output;
} else {
    echo "DNR";
} */


//function to get student response and question testCases
function getQuestion($testID, $qId){
    $post = array("testId"=>$testID, "qId"=>$qId, "page"=>"agGetQuestion");
    var_dump($post);
    $ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/backend_grades.php'); 
    //$ch = curl_init('https://afsaccess4.njit.edu/~pn253/test.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, false);

    // should return JSON like depicted bellow
    $response = curl_exec($ch);
    curl_close($ch);
    echo "Response from getQuestion: " . $response . "<br>";
    $data = json_decode($response, true);
    $funcName = substr($data["testCase1"]["runStat"], 0, strpos($data["testCase1"]["runStat"], '('));
    $data["funcName"] = $funcName;
    //var_dump($data);
    return $data;
}
/*
Will request student id, test ids(plural?) - 1 student only so id is to send back to backend
Will go through test ids (first make it work with one test being graded - no need to loop through)
Request number of questions in the test (this will be for the loop to grade all the questions)
Will then request question by question to grade (request student response for test # and question #, request testCases for question #)

Example response: already have student id & test id
    Request test 5 question 4
    {
        "qPoints" = 50,
        "numCases" = 2,
        "studentResponse" = "response to question",
        "funcName" = "function name", (don't worry about this I'll get it myself)
        "testCase0" =
            {
                "runStat" = "the call to function to run - funcName(inputValues)",
                "expected" = "value that is expected from running the above function call"
            }
        "testCase1" =
            {
                "runStat" = "the call to function to run - funcName(inputValues)",
                "expected" = "value that is expected from running the above function call"
            }
    }

*/

function getQuestionNum($testID){
    $post = array("testId"=>$testID, "page"=>"agGetQInfo");
    $ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/backend_grades.php'); 
    //$ch = curl_init('https://afsaccess4.njit.edu/~pn253/test.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    echo "Response from getQuestionNum: " . $response . "<br>";
    // should return number of questions that test has
    /*
        example JSON:
        {
            "numQuestions" : number of questions,
            "question_ids" : array of question ids
        }
    */
    return json_decode($response, true);
}

function sendQuestionGrade($testID, $qID, $numCases, $qPoints, $gradeArray){
    //calculate the total points for question by subing any point deductions
    if(array_key_exists("constraints", $gradeArray))
        $casePoints = ($qPoints - 10)/$numCases;
    else
        $casePoints = ($qPoints - 5)/$numCases;
    $casePoints = round($casePoints, 2);
    $totalGrade = $qPoints + $gradeArray["funcName"]["correct"];
    //echo $totalGrade;
    $gradeArray["funcName"]["correct"] = 5 + $gradeArray["funcName"]["correct"];
    if(array_key_exists("constraints", $gradeArray)){
        //echo $gradeArray["constraints"]["correct"];
        $totalGrade += $gradeArray["constraints"]["correct"];
        $gradeArray["constraints"]["correct"] = 5 + $gradeArray["constraints"]["correct"];
    }
    //echo $totalGrade;
    for($k = 1; $k <= $numCases; $k++){
        $totalGrade = $totalGrade + ($casePoints * $gradeArray['testCase' . $k]['correct']);
        $gradeArray['testCase' . $k]['correct'] = $casePoints + ($gradeArray['testCase' . $k]['correct'] * $casePoints);
        $gradeArray['testCase' . $k]['max_points'] = $casePoints;
        //$gradeArray['testCase' . $k]['max_points'] = $casePoints;
    }
    $gradeArray["totalGrade"] = $totalGrade . "/" . $qPoints;
    /*
        Should be sending grade array that JSON should look like:
        {
            "funcName" : -5;
            "testCase0" : {
                "correct" : 0,
                "output" : "output from running the function"
            }
            "testCase1" : {
                "correct" : -10,
                "output" : "output from running the function"
            }
            "totalGrade" : 10/out of total
        }
    */
    //var_dump($gradeArray);
    $post = array("testId"=>$testID, "qId"=>(int)$qID, "grade"=>json_encode($gradeArray), "page"=>"agSendGrade");
    //echo "<br>";
    var_dump($post);
    echo "<br><br>";
    $ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/backend_grades.php');  
    //$ch = curl_init('https://afsaccess4.njit.edu/~pn253/test.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    
    $d = json_decode($response, true);
    return $d["status"];
}

$testId = (int)$_POST["testId"];
error_log($testId);
//$testId = 32;

$qIdArray = getQuestionNum($testId);
for($i = 0; $i < $qIdArray["numQuestions"]; $i++){
    $qData = getQuestion($testId, $qIdArray["question_ids"][$i]);
    //var_dump($qData);
    $qGrade = array();
    $funcName = $qData["funcName"];

    $check = trim(substr($qData["studentResponse"], 3, (strpos($qData["studentResponse"], '(') - 3)));
    $qGrade["funcName"] = array();
    $qGrade["funcName"]["max_points"] = 5;
    if($check != $funcName){
        $qGrade["funcName"]["correct"] = -5;
        $qData["studentResponse"] = str_replace($check, $funcName, $qData["studentResponse"]);
        $qGrade["funcName"]["output"] = $check;
        $qGrade["funcName"]["expected"] = $funcName;
    } else {
        $qGrade["funcName"]["correct"] = 0;
        $qGrade["funcName"]["output"] = $check;
        $qGrade["funcName"]["expected"] = $funcName;
    }
    if($qData["constraints"] != "none"){
        $qGrade["constraints"] = array();
        $qGrade["constraints"]["max_points"] = 5;
    }
    if($qData["constraints"] == "for loop"){
        if(!strpos($qData["studentResponse"], "for")){
            $qGrade["constraints"]["correct"] = -5;
            $qGrade["constraints"]["expected"] = $qData["constraints"];
        } else {
            $qGrade["constraints"]["correct"] = 0;
            $qGrade["constraints"]["expected"] = $qData["constraints"];
        }
    }else if($qData["constraints"] == "while loop"){
        if(!strpos($qData["studentResponse"], "while")){
            $qGrade["constraints"]["correct"] = -5;
            $qGrade["constraints"]["expected"] = $qData["constraints"];
        } else {
            $qGrade["constraints"]["correct"] = 0;
            $qGrade["constraints"]["expected"] = $qData["constraints"];
        }

    }else if($qData["constraints"] == "recursion"){
        $sub = substr($qData["studentResponse"], strpos($qData["studentResponse"], '('));
        echo $sub;
        if(!strpos($sub, $funcName)){
            $qGrade["constraints"]["correct"] = -5;
            $qGrade["constraints"]["expected"] = $qData["constraints"];
        } else {
            $qGrade["constraints"]["correct"] = 0;
            $qGrade["constraints"]["expected"] = $qData["constraints"];
        }

    }

    for($j = 1; $j <= $qData["numCases"]; $j++){
        $case = "testCase" . $j;
        $runFile = fopen("resTest.py", "w");
        fwrite($runFile, $qData["studentResponse"] . "\n\nprint(" . $qData[$case]["runStat"] . ")");
        fclose($runFile);
        
        //exec python file using /afs/cad/linux/anaconda3.8/anaconda/bin/python3
        $command = "/afs/cad/linux/anaconda3.8/anaconda/bin/python3 2>&1 resTest.py";
        $output = shell_exec($command);
        if((int)$output == (int)$qData[$case]["expected"]){
            $qGrade[$case]["correct"] = 0;
        } else {
            $qGrade[$case]["correct"] = -1;
        }
        
        if(strpos($output, "resTest.py") === false){
            $qGrade[$case]["output"] = trim($output);
        } else {
            $qGrade[$case]["output"] = "DNR";
        }
        //$questionRes[$case]["output"] = $output;
    }
    sendQuestionGrade($testId, $qIdArray["question_ids"][$i], $qData["numCases"], $qData["qPoints"], $qGrade);
}
?>