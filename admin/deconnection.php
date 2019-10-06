<?php
@session_start();

@session_destroy();
unset($_SESSION["pseudoPsv"]);
unset($_SESSION["nomsPsv"]);				
unset($_SESSION["avatarPsv"]);
unset($_SESSION["rolePsv"]);
unset($_SESSION['tokenPsv']);

echo"<meta HTTP-EQUIV='Refresh' CONTENT='0; URL=index.php'>";
?>