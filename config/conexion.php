<?php
$host 	= 'localhost';
$nom 	= 'root';
$pass 	= '';
$db 	= 'sisfactura';

$con = mysqli_connect($host, $nom, $pass, $db);

if (!$con) 
{
  die("Error en la conexión: " . mysqli_connect_error());
}	
?>
