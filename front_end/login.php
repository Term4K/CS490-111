<?php
if(isset($_POST["ucid"]) && isset($_POST["pwd"])){
    $post = array("ucid"=>$_POST["ucid"], "pass"=>$_POST["pwd"]);
    $ch = curl_init('https://afsaccess4.njit.edu/~pn253/login.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Couldn\'t send request: ' . curl_error($ch));
    } else {
        // check the HTTP status code of the request
        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus == 200) {
        } else {
            error_log('Request failed: HTTP status code: ' . $resultStatus);
        }
    }

    curl_close($ch);

    $data = json_decode($response, true);
    
    if($data["login"] == 1){
        if($data["type"] == "TEACHER"){
            header("Location: https://afsaccess4.njit.edu/~pn253/front_end/teacher.php");
            die();
        } else {
            header("Location: https://afsaccess4.njit.edu/~pn253/front_end/student.php");
            die();
        }
    } else {
        echo "<h5>Incorrect Login<h5>";
    }
}

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


<html>
	</head> 
	<body>
    <div class="container-fluid">
        <h1> Beta CS490 Milestone Login Page</h1> 
        <form action  = "https://afsaccess4.njit.edu/~pn253/front_end/login.php" method="post">
            <label for="ucid">UCID:</label>
            <input type="text" id="ucid" name="ucid"><br><br>
            <label for="pwd">Password:</label>
            <input type="password" id="pwd" name="pwd"><br><br>
            <input type="submit" value="Submit">
        </form>
    </div>
	</body>
</html>

