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
            <a class="navbar-brand" href="teacher_exam.php">Make Exam</a>
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
            <h2>Make a Question</h2>

            <form action  = "teacher.php" method="post" onsubmit="return false">
                Select Difficulty:
            	<select name="diff" id="diff">
            		<option value="Easy">Easy</option>
            		<option value="Medium">Medium</option>
            		<option value="Hard">Hard</option>
            	</select><br>
            	Select Type of Question:
            	<select name="type" id="type">
            		<option value="for loop">for loop</option>
            		<option value="while loop">while loop</option>
            		<option value="recursion">Recursion</option>
            	</select><br>
                Select Constraint (if any):
            	<select name="const" id="const">
                    <option value="none">none</option>
            		<option value="for loop">for loop</option>
            		<option value="while loop">while loop</option>
            		<option value="recursion">Recursion</option>
            	</select><br>
                
                <label for="question">Enter Question:</label><br>
                <textarea name="question" id="question" cols="50" rows="10" required></textarea><br><br>
                <div id="testCases">
                <label for="testCase1">Test Case 1: </label><br>
                <input type="text" id="testCase1" name="testCase1" required><br><br>
                <label for="ts1_expected">Expected Value for Test Case 1: </label><br>
                <input type="text" name="ts1_expected" id="ts1_expected" required><br><br>
                <label for="testCase1">Test Case 2: </label><br>
                <input type="text" id="testCase2" name="testCase2" required><br><br>
                <label for="ts2_expected">Expected Value for Test Case 2: </label><br>
                <input type="text" name="ts2_expected" id="ts2_expected" required><br><br>
                </div>
                <input type="submit" value="Add Test Case" id="addTCase" onclick="addTestCase()"><br>
                <input type="submit" value="Submit" onclick="makeQuestion()">
            </form>
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
            <table id='qTable' style="width:80%;height:10%;"></table> 
    </div>
</div>
</div>

<script>
    var testCases = 2;
    window.onload = function(){
        getQuestions();
    }

    function getQuestions(){
        var table = "<tr><th>Question</th><th>Type</th><th>Difficulty</th></tr>"
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
                                table += "<tr><td>" + this[key]['question'] + "</td><td>" + this[key]['type'] + "</td><td>" + this[key]['diff'] + "</td></tr>";
                        } else {
                            table += "<tr><td>" + this[key]['question'] + "</td><td>" + this[key]['type'] + "</td><td>" + this[key]['diff'] + "</td></tr>";
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

    function addTestCase(){
        var cont = document.getElementById("testCases");
        testCases += 1;
        if(testCases <= 5){
            cont.appendChild(document.createTextNode("Test Case " + testCases + ":"));
            cont.appendChild(document.createElement("br"));
            var input1 = document.createElement("input");
            input1.type = "text";
            input1.name = "testCase" + testCases;
            input1.id = "testCase" + testCases;
            cont.appendChild(input1);
            cont.appendChild(document.createElement("br"));
            cont.appendChild(document.createElement("br"));
            cont.appendChild(document.createTextNode("Expected value for Test Case " + testCases + ":"));
            cont.appendChild(document.createElement("br"));
            var input = document.createElement("input");
            input.type = "text";
            input.name = "ts" + testCases + "_expected";
            input.id = "ts" + testCases + "_expected";
            cont.appendChild(input);
            cont.appendChild(document.createElement("br"));
            cont.appendChild(document.createElement("br"));
        }
    }

    function makeQuestion(){
        var question = {};
        question['diff'] = document.getElementById('diff').value;
        question['type'] = document.getElementById('type').value;
        question['question'] = document.getElementById('question').value;
        question['constraint'] = document.getElementById('const').value;
        question['numTestCases'] = testCases;

        for(var i = 1; i <= testCases; i++){
            question['testCase' + i] = document.getElementById('testCase' + i).value;
            question['ts'+i+'_expected'] = document.getElementById('ts'+i+'_expected').value;
        }
        
        var req = new XMLHttpRequest();
        req.open("POST", "sendQuestion.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                if(ret != null){
                    location.reload()
                }
            }
        }
        console.log(question);
        req.send("json=" + encodeURIComponent(JSON.stringify(question)));
    }
</script>
