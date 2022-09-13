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
        <br><h2 id="examName">Exams</h2><br>
        <table id='examTable' style="margin-left:auto;margin-right:auto;"></table> 
        <input type="submit" value="Submit" onclick="submitQuestions()">
    </div>
</div>

<script>

    window.onload = function(){
        getExamQuestions();
    }

    function getExamQuestions(){
        var queryDict = {}
        location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]});
        var examId = parseInt(queryDict['exam']);
        console.log(examId);
        
        var table = "<tr><th style='width: 300px;'>Question</th><th>Answer</th><th style='width: 200px;'></th></tr>"
        var req = new XMLHttpRequest();
        req.open("POST", "examQuestions.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                if(ret != null){
                document.getElementById('examName').innerHTML = ret["examName"];
                var q = ret['questions'];
                Object.keys(q).forEach(function(key, index) {
                    console.log(this[key]);
                    if(this[key]['question'] !== ''){
                        table += "<tr height='300px' id=" + key + "><td>" + this[key]['question'] + "</td><td><textarea onkeydown=\"if(event.keyCode===9){var v=this.value,s=this.selectionStart,e=this.selectionEnd;this.value=v.substring(0, s)+'\t'+v.substring(e);this.selectionStart=this.selectionEnd=s+1;return false;}\" name='ans' rows='8' cols='50' id=" + key + "></textarea></td><td>" + this[key]['points'] + " Points</td></tr>";
                    }
                }, q);
            }
                document.getElementById('examTable').innerHTML = table;
            }
        }
        var send = {};
        send["testId"] = examId;
        req.send("json=" + encodeURIComponent(JSON.stringify(send)));
    }

    function submitQuestions(){
        var queryDict = {}
        location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]});
        var examId = parseInt(queryDict['exam']);
        console.log(examId);
        var send = {};
        send["testId"] = examId;

        var submissions = {};
        var ans = document.querySelectorAll('textarea');
        var i;
        for(i = 0; i < ans.length; i++){
            submissions[ans[i].id] = ans[i].value;
        }
        console.log(submissions);
        send["sub"] = JSON.stringify(submissions);

        var req = new XMLHttpRequest();
        req.open("POST", "submitExam.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                location.href="student.php";
            }
        }
        console.log(send);
        req.send("json=" + encodeURIComponent(JSON.stringify(send)));
    }
    
</script>
