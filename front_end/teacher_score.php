<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

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
<div class="container-fluid">
    <div class="text-center mb-5">
        <br><h2 id="examName">Exam</h2><br>
        <div id="gradedQ">

        </div>
        <input type='button' value='Update' onclick='updateScore()'>
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
        req.open("POST", "getScore.php", true);
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
                        var grade = JSON.parse(arr[key]);
                        count++;
                        var table = "<tr><th style='width: 250px;'>Test Case</th><th style='width: 150px;'>Expected</th><th style='width: 150px;'>Result</th><th style='width: 100px;'>Score</th><th>Change</th></tr>";
                        Object.keys(grade).forEach(function(testCase, i) {
                            //var testInfo = getTestCase(key, testCase);
                            //console.log(testInfo);
                            if(testCase !== "" && testCase != "totalGrade"){
                                if(testCase == "funcName"){   
                                    table += "<tr height='45px' id=" + testCase + "><td>Correct Function Name</td><td>" + grade[testCase]["expected"] + "</td><td>" + grade[testCase]["output"] + "</td><td>" + grade[testCase]["correct"] + "</td><td><input size='2' type='text' value='" + grade[testCase]["correct"] + "' id='funcName" + key + "'></td></tr>";
                                } else if(testCase == "constraints"){
                                    table += "<tr height='45px' id=" + testCase + "><td>Constraint</td><td>" + grade[testCase]["expected"] + "</td><td></td><td>" + grade[testCase]["correct"] + "</td><td><input size='2' type='text' value='" + grade[testCase]["correct"] + "' id='constraint" + key + "'></td></tr>";
                                } else {
                                    table += "<tr height='45px' id=" + testCase + "><td>" + ret[key][testCase][0] + "</td><td>" + ret[key][testCase][1] + "</td><td>" + grade[testCase]["output"] + "</td><td id='" + testCase + key + "'>" + grade[testCase]["correct"] + "</td><td><input size='2' type='text' value='" + grade[testCase]["correct"] + "' id='testCase" + key + "'></td></tr>";
                                }
                            } else {
                                table += "<tr height='45px' id=" + testCase + "><td>Total Score</td><td></td><td></td><td></td><td>" + grade[testCase] + "</td></tr>";
                            }
                        }, arr);
                        var tbl = document.createElement('table');
                        tbl.id = key;
                        tbl.style = "margin-left:auto;margin-right:auto;";
                        cont.appendChild(tbl);
                        document.getElementById(key).innerHTML = table;
                        var comment = document.createElement('textarea');
                        comment.value = "Put comment to student here";
                        comment.id = "comment" + key;
                        comment.row = 4;
                        comment.cols = 50;
                        cont.appendChild(comment)
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

    function updateScore(){
        var queryDict = {}
        location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]});
        var examId = parseInt(queryDict['exam']);
        //console.log(examId);
        var tables = document.querySelectorAll('table');
        var updatedGrade = {};
        
        for(var i = 0; i < tables.length; i++){
            var qId = Math.abs(tables[i].id);
            updatedGrade[qId] = {};
            console.log(qId);
            var diff = 0;
            var temp = {};
            var origGrade = JSON.parse(gradeArray["grade"][qId]);
            temp["funcName"] = {};
            temp["funcName"]["correct"] = document.getElementById("funcName" + qId).value;
            temp["funcName"]["expected"] = origGrade["funcName"]["expected"];
            temp["funcName"]["output"] = origGrade["funcName"]["output"];
            if("constraints" in origGrade){
                temp["constraints"] = {};
                temp["constraints"]["correct"] = document.getElementById("constraint" + qId).value;
                temp["constraints"]["expected"] = origGrade["constraints"]["expected"];
                diff += (temp["constraints"]["correct"] - parseInt(origGrade["constraints"]["correct"]));
            }
            diff += (temp["funcName"]["correct"] - parseInt(origGrade["funcName"]["correct"]));
            var testCases = document.querySelectorAll('input[id=testCase' + qId + ']');
            var count = 1;
            for(var j = 0; j < testCases.length; j++){
                //console.log(temp)
                temp["testCase" + count] = {};
                temp["testCase" + count]["correct"] = testCases[j].value;
                temp["testCase" + count]["output"] = origGrade["testCase" + count]["output"];
                diff += testCases[j].value - origGrade["testCase" + count]["correct"];
                //console.log(diff);
                count++;
            }
            var totalGrade = origGrade["totalGrade"].split('/');
            temp["totalGrade"] = (parseInt(totalGrade[0]) + diff) + "/" + totalGrade[1];
            updatedGrade[qId]["grade"] = JSON.stringify(temp);
            updatedGrade[qId]["comment"] = document.getElementById("comment"+qId).value;
        }
        console.log(updatedGrade);
        var req = new XMLHttpRequest();
        req.open("POST", "updateExam.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                if(ret != null){
                    location.reload();
                }
            }
        }
        var send = {};
        send['testId'] = examId;
        send['grades'] = JSON.stringify(updatedGrade);
        console.log(send);
        req.send("json=" + encodeURIComponent(JSON.stringify(send)));
    }

</script>
