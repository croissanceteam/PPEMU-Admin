<?php

//echo "Initial".date('s@W')."$";
// echo password_hash('123',PASSWORD_BCRYPT);

// $date_export = "2019-09-28 19:10:06";
// echo date('d/m/Y à H:i', strtotime($date_export));

$txt= '{"rep_updated":"'.date('d F Y, H:i:s').'"}';
//        $txt= '{"updated":"27 Juillet 2019, 12:00:30"}';
try {
    $filen="../../mobile/date.json";
    $fp = fopen($filen, 'w');
    fwrite($fp, $txt);
    fclose($fp);
    
    $file=file_get_contents("../../mobile/date.json");
    echo $file;
} catch (\Throwable $th) {
    echo $th->getMessage();
}


