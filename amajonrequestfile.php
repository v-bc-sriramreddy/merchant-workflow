<?php
$c = curl_init('https://api.amazon.com/auth/o2/tokeninfo?access_token='. urlencode($_REQUEST['access_token']));
var_dump($c);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

$r = curl_exec($c);
var_dump($r);
curl_close($c);

$d = json_decode($r);

if ($d->aud != 'amzn1.application-oa2-client.a1d6b0ae49e744818a936ffac6d53a4c') {

 // the access token does not belong to us

 header('HTTP/1.1 404 Not Found');

 echo 'Page not found';

 exit;

}
?>