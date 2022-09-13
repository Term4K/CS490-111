<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <ul>
            <a class="navbar-brand" href="student.php">View Exams</a>
            <a class="navbar-brand" href="login.php">Logout</a>
        </ul>
        <ul class="navbar-nav ml-auto">
            <a class="navbar-brand" href="#">Student</a>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    <div class="text-center mb-5">
        <br><h2 id="examName">Exam</h2><br>
        <div id="gradedQ">

        </div>
    </div>
</div>

<script>
    var gradeArray;
    window.onload = function(){
        getScore();
    }

    function getScore(){
        var queryDict = {}
        location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]});
        var examId = parseInt(queryDict['exam']);
        //console.log(examId);
        var cont = document.getElementById("gradedQ");

        var req = new XMLHttpRequest();
        req.open("POST", "getStudentScore.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                gradeArray = ret;
                var arr = ret["grade"];
                var count = 1;
                if(ret != null){
                    Object.keys(arr).forEach(function(key, index) {
                        console.log(this[key]);
                        cont.appendChild(document.createElement("HEADER"));
                        cont.appendChild(document.createTextNode("Question " + count));
                        cont.appendChild(document.createElement("H2"));
                        var grade = JSON.parse(arr[key]["auto"]);
                        teachGrade = JSON.parse(arr[key]["teacher"]);
                        count++;
                        var table = "<tr><th style='width: 200px;'>Test Case</th><th style='width: 150px;'>Expected</th><th style='width: 120px;'>Result</th><th style='width: 175px;'>Auto Grader Score</th><th>Instructor Score</th></tr>";
                        Object.keys(grade).forEach(function(testCase, i) {
                            //var testInfo = getTestCase(key, testCase);
                            //console.log(testInfo);
                            if(testCase !== "" && testCase != "totalGrade"){
                                if(testCase == "funcName"){   
                                    table += "<tr height='45px' id=" + testCase + "><td>Correct Function Name</td><td>" + grade[testCase]["expected"] + "</td><td>" + grade[testCase]["output"] + "</td><td>" + grade[testCase]["correct"] + "</td><td>" + teachGrade[testCase]["correct"] + "</td></tr>";
                                } else if(testCase == "constraints"){
                                    table += "<tr height='45px' id=" + testCase + "><td>Constraint</td><td>" + grade[testCase]["expected"] + "</td><td></td><td>" + grade[testCase]["correct"] + "</td><td>" + teachGrade[testCase]["correct"] + "</td></tr>";
                                } else {
                                    table += "<tr height='45px' id=" + testCase + "><td>" + ret[key][testCase][0] + "</td><td>" + ret[key][testCase][1] + "</td><td>" + grade[testCase]["output"] + "</td><td id='" + testCase + key + "'>" + grade[testCase]["correct"] + "</td><td>" + teachGrade[testCase]["correct"] + "</td></tr>";
                                }
                            } else {
                                table += "<tr height='45px' id=" + testCase + "><td>Total Score</td><td></td><td></td><td>" + grade[testCase] + "</td><td>" + teachGrade[testCase] + "</td></tr>";
                            }
                        }, arr);
                        var tbl = document.createElement('table');
                        tbl.id = key;
                        tbl.style = "margin-left:auto;margin-right:auto;";
                        cont.appendChild(tbl);
                        table += "<tr height='45px'><td>Comment</td><td></td><td></td><td></td><td>" + arr[key]["comment"] + "</td></tr>"
                        document.getElementById(key).innerHTML = table;
                        
                        cont.appendChild(document.createElement("br"));
                        cont.appendChild(document.createElement("br"));
                    }, ret);
                }
            }
        }
        var send = {};
        send["testId"] = examId;
        req.send("json=" + encodeURIComponent(JSON.stringify(send)));
    }

</script>













<script>
/*
    var gradeArray;
    window.onload = function(){
        getScore();
    }

    function getScore(){
        var queryDict = {}
        location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]});
        var examId = parseInt(queryDict['exam']);
        //console.log(examId);
        var cont = document.getElementById("gradedQ");

        var req = new XMLHttpRequest();
        req.open("POST", "getStudentScore.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                gradeArray = ret;
                var count = 1;
                if(ret != null){
                    Object.keys(ret).forEach(function(key, index) {
                        console.log(this[key]);
                        cont.appendChild(document.createElement("HEADER"));
                        cont.appendChild(document.createTextNode("Question " + count));
                        cont.appendChild(document.createElement("H2"));
                        var gradeS = JSON.parse(ret[key]["auto"]);
                        var gradeT = JSON.parse(ret[key]["teacher"]);
                        count++;
                        var table = "<tr><th style='width: 200px;'>Test Case</th><th style='width: 150px;'>Expected</th><th style='width: 120px;'>Result</th><th style='width: 175px;'>Auto Grader Score</th><th>Instructor Score</th></tr>";
                        Object.keys(gradeS).forEach(function(testCase, i) {
                            var testInfo = getTestCase(key, testCase);
                            if(testCase !== "" && testCase != "totalGrade"){
                                if(testCase == "funcName"){   
                                    table += "<tr height='45px' id=" + testCase + "><td>" + testCase + "</td><td></td><td></td><td>" + gradeS[testCase] + "</td><td>" + gradeT[testCase] + "</td></tr>";
                                } else {
                                    table += "<tr height='45px' id=" + testCase + "><td>" + testInfo[0] + "</td><td>" + testInfo[1] + "</td><td>" + gradeS[testCase]["output"] + "</td><td id='" + testCase + key + "'>" + gradeS[testCase]["correct"] + "</td><td>" + gradeT[testCase]["correct"] + "</td></tr>";
                                }
                            } else {
                                table += "<tr height='45px' id=" + testCase + "><td>Total Score</td><td></td><td></td><td>" + gradeS[testCase] + "</td><td>" + gradeT[testCase] + "</td></tr>";
                            }
                        }, gradeS);
                        var tbl = document.createElement('table');
                        tbl.id = key;
                        tbl.style = "margin-left:auto;margin-right:auto;";
                        cont.appendChild(tbl);
                        document.getElementById(key).innerHTML = table;
                        cont.appendChild(document.createElement("br"));
                        cont.appendChild(document.createElement("br"));
                    }, ret);
                }
            }
        }
        req.send();
    }

    function getTestCase(qId, testName){
        return ["def num(2)", 4];
        /* var req = new XMLHttpRequest();
        req.open("POST", "gradeExams.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                if(ret != null){
                
                }
            }
        }
        req.send();
    }
    */
</script>
