<?php
require_once ("const.php"); //archivo que contiene los datos de las constantes para la conexion a la BD
include ("BdCursos.php");
include ("BdEmpresas.php");
/*$bd = new BdPersonal();
echo $bd->agregarPersonal('91804082L','Alberto Sánchez España',1);
$bd2 = new BdPersonal();
echo $bd2->agregarPersonal('X8225981P','Marta Sánchez España',2);*/
$bd = new BdCursos();
var_dump($bd->mostrarCodEmpresaPasandoNombre('adios'));