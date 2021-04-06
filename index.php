<?php 
$dominio =preg_replace('#(\?.*)$#', '', $_SERVER['SERVER_NAME']);
if($dominio=='ts6.aguasdebuga.com'){
    header("location: login/index_companies.php?company_id=4");
    exit();
}

header("location: login/index.php");
?>