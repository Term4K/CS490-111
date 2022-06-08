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
function getQuestion($studID, $testID, $qNum){
    $post = array("studID"=>$studID, "testID"=>$testID, "qNum"=>$qNum);
    //$ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/getStudent_responses.php'); 
    $ch = curl_init('https://afsaccess4.njit.edu/~pn253/test.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, false);

    // should return JSON like depicted bellow
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    $funcName = substr($data["testCase0"]["runStat"], 0, strpos($data["testCase0"]["runStat"], '('));
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
        "qIdent" = 23, (not really necessary)
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
    $post = array("testID"=>$testID);
    //$ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/getTest.php'); 
    $ch = curl_init('https://afsaccess4.njit.edu/~pn253/test.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    // should return number of questions that test has
    /*
        example JSON:
        {
            "testID" : id of test,
            "numQuestions" : number of questions
        }
    */
    return json_decode($response, true)["numQuestions"];
}

function sendQuestionGrade($studentID, $testID, $qNum, $numCases, $qPoints, $gradeArray){
    //calculate the total points for question by subing any point deductions
    $casePoints = ($qPoints - 5)/$numCases;
    $totalGrade = $qPoints + $gradeArray["funcName"];
    for($k = 0; $k < $numCases; $k++){
        $totalGrade = $totalGrade + ($casePoints * $gradeArray['testCase' . $k]['correct']);
    }
    $gradeArray["totalGrade"] = $totalGrade;
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
            "totalGrade" : 10
        }
    */
    //var_dump($gradeArray);
    echo json_encode($gradeArray);
    $post = array("studID"=>$studentID, "testID"=>$testID, "qNum"=>$qNum, "grade"=>json_encode($gradeArray));

    //$ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/saveGrade.php'); 
    $ch = curl_init('https://afsaccess4.njit.edu/~pn253/test.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    
    $d = json_decode($response, true);
    return $d["status"];
}

//POST to get student id and test id(s?)
//resulting array would be something like this - studID => ##, testID => testID
$post = array("submit"=>1);
//$ch = curl_init('https://afsaccess4.njit.edu/~as3526/CS490/getGrade.php'); 
$ch = curl_init('https://afsaccess4.njit.edu/~pn253/test.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);
$numQ = getQuestionNum($data["testID"]);
for($i = 0; $i < $numQ; $i++){
    $qData = getQuestion($data["studID"], $data["testID"], $i);
    //var_dump($qData);
    $qGrade = array();
    $funcName = $qData["funcName"];

    $check = trim(substr($qData["studentResponse"], 3, (strpos($qData["studentResponse"], '(') - 3)));
    
    if($check != $funcName){
        $qGrade["funcName"] = -5;
        $qData["studentResponse"] = str_replace($check, $funcName, $qData["studentResponse"]);
    } else {
        $qGrade["funcName"] = 0;
    }

    for($j = 0; $j < $qData["numCases"]; $j++){
        $case = "testCase" . $j;
        $runFile = fopen("resTest.py", "w");
        fwrite($runFile, $qData["studentResponse"] . "\n\nprint(" . $qData[$case]["runStat"] . ")");
        fclose($runFile);
        
        //exec python file using /afs/cad/linux/anaconda3.8/anaconda/bin/python3
        $command = "/afs/cad/linux/anaconda3.8/anaconda/bin/python3 2>&1 resTest.py";
        $output = shell_exec($command);
        if($output == $qData[$case]["expected"]){
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
    sendQuestionGrade($data["studID"], $data["testID"], $i, $qData["numCases"], $qData["qPoints"], $qGrade);
}
?>