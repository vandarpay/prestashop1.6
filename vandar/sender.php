<?php
function send($api_key, $amount, $callback_url)
{
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,'https://vandar.io/api/ipg/send');
    curl_setopt($ch,CURLOPT_POSTFIELDS,"api_key=$api_key&amount=$amount&callback_url=$callback_url");
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function verify($api_key, $token)
{
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,'https://vandar.io/api/ipg/verify');
    curl_setopt($ch,CURLOPT_POSTFIELDS,"api_key=$api_key&token=$token");
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}