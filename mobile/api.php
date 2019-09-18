<?php
 header("Access-Control-Allow-Origin: *");
 
if (isset($_GET['date']) && $_GET['date']=='true') {
    getLastUpdate();
}
function getLastUpdate(){
    $file=file_get_contents("./date.json");
    echo $file;
}