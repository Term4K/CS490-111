<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<?php
if(isset($_POST["question"]) && isset($_POST["testCase1"]) && isset($_POST["ts1_expected"]) && isset($_POST["testCase2"]) && isset($_POST["ts2_expected"])){
    $post = array("page"=>0, "diff"=>$_POST["diff"], "type"=>$_POST["type"], "question"=>$_POST["question"], "testCase1"=>$_POST["testCase1"], "ts1_expected"=>$_POST["ts1_expected"], "testCase2"=>$_POST["testCase2"], "ts2_expected"=>$_POST["ts2_expected"]);
    $ch = curl_init('https://afsaccess4.njit.edu/~pn253/middle.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    curl_close($ch);
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <ul>
            <a class="navbar-brand" href="teacher.php">Make Question</a>
            <a class="navbar-brand" href="teacher_view_score.php">View Scores</a>
            <a class="navbar-brand" href="login.php">Logout</a>
        </ul>
        <ul class="navbar-nav ml-auto">
            <a class="navbar-brand" href="#">Teacher</a>
        </ul>
    </div>
</nav>
<div class="row">
<div class="col-lg-6 col-sm-12 left border border-secondary">
    <div class="mb-5">
        <div class="container-fluid">
            <h2>Make a Exam</h2>

            <form onsubmit="makeExam()">
                
                <label for="examName">Enter Name: </label><br>
                <input type="text" id="examName" name="examName" required><br><br>
                <table id='examBank' cellpadding="10" cellspacing="10" style="width:90%;height:10%;"></table>
                <input type="submit" value="Submit">
            </form><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
            <div id=examBank>

            </div>
        </div>
    </div>
</div>

<div class="col-lg-6 col-sm-12 right border border-secondary">
    <div class="text-center mb-5">
        <h2>Question Bank</h2>
            Select Difficulty:
            <select name="diffBank" id="diffBank">
        		<option value="Easy">Easy</option>
        		<option value="Medium">Medium</option>
        		<option value="Hard">Hard</option>
           	</select>
           	Select Type of Question:
        	<select name="typeBank" id="typeBank">
        		<option value="for loop">for loop</option>
        		<option value="while loop">while loop</option>
        		<option value="recursion">recursion</option>
        	</select><br>
            Keyword
            <input type="text" id="filt">
            <input type="submit" value="Filter" id="qGet" onclick="getQuestions()"><br>
            <table id='qTable' cellpadding="10" cellspacing="10" style="width:90%;height:10%;"></table> 
            <input type="submit" value="Add Questions" onclick="addQuestions()"><br>
    </div>
</div>
</div>

<script>
    var testCases = 2;
    var questionArray = {};
    var numQuestions = 0;
    window.onload = function(){
        getQuestions();
    }

    function getQuestions(){
        var table = "<tr><th>Select</th><th style='width:50%'>Question</th><th>Type</th><th>Difficulty</th><th>Points</th></tr>"
        var filt = document.getElementById('filt').value;
        var req = new XMLHttpRequest();
        req.open("POST", "qBank.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                if(ret != null){
                Object.keys(ret).forEach(function(key, index) {
                    console.log(this[key]);
                    if(this[key]['question'] !== ''){
                        if(filt != ""){
                            if(this[key]['question'].includes(filt))
                                table += "<tr id=" + key + "><td><input id=" + key + " name='addQ' type='checkbox' </td><td>" + this[key]['question'] + "</td><td>" + this[key]['type'] + "</td><td>" + this[key]['diff'] + "</td><td><input size='3' type='text' id='points" + key + "'></td></tr>"
                        } else {
                            table += "<tr id=" + key + "><td><input id=" + key + " name='addQ' type='checkbox' </td><td>" + this[key]['question'] + "</td><td>" + this[key]['type'] + "</td><td>" + this[key]['diff'] + "</td><td><input size='3' type='text' id='points" + key + "'></td></tr>"
                        }
                    }
                }, ret);
            }
                document.getElementById('qTable').innerHTML = table;
            }
        }
        var diff = document.getElementById('diffBank').value;
        var type = document.getElementById('typeBank').value;
        var arr = {};
        arr['diff'] = diff;
        arr['type'] = type;
        req.send("json=" + encodeURIComponent(JSON.stringify(arr)));
    }

    function addQuestions(){
        var exam = {};
        exam['name'] = document.getElementById('examName').value;
        var questions = document.querySelectorAll('input[type=checkbox]:checked');
        numQuestions += questions.length;

        var i;
        for(i = 0; i < questions.length; i++){
            questionArray[questions[i].id] = document.getElementById('points' + questions[i].id).value;
            var cont = document.getElementById("examBank");
            questions[i].checked = false;
            cont.appendChild(document.getElementById(questions[i].id));

        }
        console.log(questionArray);
        console.log(numQuestions);
        getQuestions();
    }

    function makeExam(){
        console.log("Making Exam");
        console.log(questionArray);
        console.log(numQuestions);
        var exam = {};
        exam['name'] = document.getElementById('examName').value;
        exam['numQuestions'] = numQuestions;

        exam['questions'] = JSON.stringify(questionArray);
        console.log(exam);

        var req = new XMLHttpRequest();
        req.open("POST", "makeExam.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
            }
        }
        req.send("json=" + encodeURIComponent(JSON.stringify(exam)));
        return true;
    }
</script>