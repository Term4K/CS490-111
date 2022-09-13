<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <ul>
            <a class="navbar-brand" href="student_view_score.php">View Score</a>
            <a class="navbar-brand" href="login.php">Logout</a>
        </ul>
        <ul class="navbar-nav ml-auto">
            <a class="navbar-brand" href="#">Student</a>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    <div class="text-center mb-5">
        <br><h2>Exams</h2><br>
        <table id='examTable' style="margin-left:auto;margin-right:auto;"></table> 
    </div>
</div>

<script>
    window.onload = function(){
        getExams();
    }

    function getExams(){
        var table = "<tr><th style='width:70%'>Exam</th><th>Options</th></tr>"
        var req = new XMLHttpRequest();
        req.open("POST", "listExams.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200){
                console.log(JSON.parse(req.responseText));
                ret = JSON.parse(req.responseText);
                if(ret != null){
                Object.keys(ret).forEach(function(key, index) {
                    console.log(this[key]);
                    if(this[key]['question'] !== ''){
                        table += "<tr id=" + key + "><td>" + this[key] + "</td><td><input type='button' value='Take' id='" + key + "' onclick='location.href=\"student_take_exam.php?exam=" + key + "\"'></td></tr>";
                    }
                }, ret);
            }
                document.getElementById('examTable').innerHTML = table;
            }
        }
        req.send();
    }
</script>
