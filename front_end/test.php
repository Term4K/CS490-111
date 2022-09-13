<?php
$post = array("ucid"=>"ashley", "pass"=>"rand");
$ch = curl_init('https://afsaccess4.njit.edu/~pn253/login.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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

error_log($response);
print_r(json_decode($response, true));

$hash1 = password_hash("random", PASSWORD_BCRYPT);
$hash2 = password_hash("rand", PASSWORD_BCRYPT);
error_log($hash1);
error_log($hash2);

error_log(password_verify("random", $hash1));
error_log(password_verify("rand", $hash2));


?>