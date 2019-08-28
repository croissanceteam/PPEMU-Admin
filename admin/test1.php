<?php
    $link='https://kf.kobotoolbox.org/assets/aN9oVWSGeVTGhyuAxxcyr5/submissions/?format=json';
    /*header("Authorization","Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    $json = curl_exec($ch);
    $obj = json_decode($json);
    echo $obj;
    */
    $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];

$context = stream_context_create($opts);
$data = file_get_contents($link, false, $context);
echo json_encode(json_decode($data));
?>