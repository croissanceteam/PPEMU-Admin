<?php
    $link='https://kf.kobotoolbox.org/assets/au2pD2CP4VRoqcwB5fvLzD/submissions/?format=json';
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

$parsed_json = json_decode($data,true);

foreach ($parsed_json as $v) {
    echo $v['Nom_du_Contr_leur'].'<br>';
 }


//$jsonIterator = new RecursiveIteratorIterator(
//    new RecursiveArrayIterator(json_decode($data, TRUE)),
//    RecursiveIteratorIterator::SELF_FIRST);
//
////$string = file_get_contents("/home/michael/test.json");
//$json_a = json_decode($data, true);
//
//echo $json_a['Nom_du_Contr_leur'];
//echo $json_a[1][Nom_du_Contr_leur];

//foreach ($jsonIterator as $key => $val) {
////    if(is_array($val)) {
//        echo $jsonIterator[$key][Nom_du_Contr_leur];
////    } else {
//        //echo "$key => $val\n";
//    //}
//}

//echo $data ;//= json_encode($data);

//$json_data = json_encode(json_decode($data));
//foreach($json_data as $v){
//   echo $v['Nom_du_Contr_leur'].'<br>';
//}

//foreach(json_decode($data) as $i => $produits){
//        //$produit=htmlentities($_POST['nomProd'][$i], ENT_QUOTES);
//
//        echo json_encode($data[$i]);
//
//    }

//echo json_encode(json_decode($data));
?>