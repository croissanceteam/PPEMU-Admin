<?php

session_start();

include_once '../Metier/Autoloader.php';
Autoloader::register();

$user = new User();

$rs = $user->all();
$list = [];
while ($data = $rs->fetch()) {
    $list [] = [
        'username'  =>  $data->username,
        'fullname'  =>  $data->fullname,
        'email'  =>  $data->mailaddress,
        'phone'  =>  $data->phone,
        'status'  =>  $data->status,
        'actions'  =>  'action'
    ];
    
}

echo json_encode($list);