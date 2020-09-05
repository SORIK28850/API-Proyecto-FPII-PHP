<?php
header("Access-Control-Allow-Origin: *"); //cabecera que evita el problema de CURL en JS
header("Access-Control-Allow-Headers: *"); //cabecera que evita el problema de CURL en JS
require_once ("BdEmpresas.php"); //archivo que contiene la clase que edita la BD empresas
require_once ("const.php"); //archivo que contiene los datos de las constantes para la conexion a la BD
require_once ("BdPersonal.php"); //archivo que contiene la clase que edita la BD personal
require_once ("BdCursos.php"); //archivo que contiene la clase que edita la BD cursos


/**
 * Este switch contiene todas las funciones de la API, devuelve los datos en formato JSON para ser
 * utilizados mediante una aplicacion web.
 */
 switch ($_POST['func']){
     /*
      * Gestion de personal de la base de datos
      */
     case "registrarPersonal":
         $resultado = false;
         $bd = new BdPersonal();    //esta funcion de la API crea usuarios nuevos en la base de datos al recibir los datos de un formulario
         if(isset($_REQUEST['dni']) && isset($_REQUEST['nombre']) && isset($_REQUEST['telefono']) && isset($_REQUEST['email'])
             && isset($_REQUEST['tipo'])){
            $resultado =  $bd->agregarPersonal($_REQUEST['dni'],$_REQUEST['nombre'],$_REQUEST['telefono'],$_REQUEST['email'],
                $_REQUEST['tipo']);
         }
         echo json_encode($resultado);
         break;
     case "modificarPersonal":
         $resultado = false;// esta funcion modifica un usuario registrado en la base de datos
         $bd = new BdPersonal();  // a traves de un formulario
         if( isset($_REQUEST['dniViejo']) && isset($_REQUEST['dni']) && isset($_REQUEST['nombre']) && isset($_REQUEST['telefono'])
             && isset($_REQUEST['email'])){
             $resultado =  $bd->modificarPersonal($_REQUEST['dniViejo'],$_REQUEST['dni'],$_REQUEST['nombre'],
                 $_REQUEST['telefono'],$_REQUEST['email']);
         }
         echo json_encode($resultado);
         break;
     case "borrarPersonal":
         $resultado = false;
         if(isset($_REQUEST['dni'])){
             $bd = new BdPersonal(); //esta parte de la API borra personal de la base de datos
             $resultado = $bd->borrarPersonal($_REQUEST['dni']);
         }
         echo json_encode($resultado);
         break;
     case "mostrarPersonal":
         $bd = new BdPersonal(); //esta parte de la API muestra el personal registrado en la base de datos
         $personal = $bd->mostrarPersonal();
         echo json_encode($personal);
         break;
     case "mostrarInstructores":
         $bd = new BdPersonal(); //esta parte de la API muestra  los instructores de la BD
         $instructores = $bd->mostrarInstructores();
         echo json_encode($instructores);
         break;
     case "mostrarAyudantes":
         $bd = new BdPersonal(); //esta parte de la API muestra a los ayudantes de la BD
         $ayudantes = $bd->mostrarAyudantes();
         echo json_encode($ayudantes);
         break;
     case "mostrarDNIAyudantes":
         $bd = new BdPersonal(); //esta parte de la API muestra los dnis de los ayudantes de la BD
         $DNIS = $bd->mostrarDNIAyudantes();
         echo json_encode($DNIS);
         break;
     case "mostrarDNISInstructores":
         $bd = new BdPersonal();// esta parte muestra los dnis de los instructores de la BD
         $DNISInstructores = $bd->mostrarDNISInstructores();
         echo json_encode($DNISInstructores);
         break;
     case "cambiarAyudanteAInstructor":
         $resultado = false;
         $bd = new BdPersonal();// esta parte cambia a personal ayudante a personal instructor
         if($_REQUEST['dniCambiar']){
             $resultado = $bd->cambiarAyudanteAInstructor($_REQUEST['dniCambiar']);
         }
         echo json_encode($resultado);
         break;
     case "mostrarDniPersonal":
         $bd = new BdPersonal();
         $DNIS = $bd->mostrarDniPersonal(); //esta parte de la API muestra los DNI del personal de la BD
         echo json_encode($DNIS);
         break;
     case "mostrarNombresPersonal":
         $bd = new BdPersonal();
         $nombres = $bd->mostrarNombresPersonal(); //esta parte de la API muestra los nombres del personal de la BD
         echo json_encode($nombres);
         break;
     /*
      * Gestion de empresas de la base de datos
      */
     case "introducirEmpresa":
         $resultado = false;
         $bd = new BdEmpresas(); //esta parte de la API introduce empresas en la BD
         if(isset($_REQUEST['nombre']) && isset($_REQUEST['telefono']) && isset($_REQUEST['provincia']) &&
             isset($_REQUEST['direccion']) && isset($_REQUEST['email'])){
             $resultado = $bd->introducirEmpresa($_REQUEST['nombre'],$_REQUEST['telefono'],
                 $_REQUEST['provincia'],$_REQUEST['direccion'],$_REQUEST['email']);
         }
         echo json_encode($resultado);
         break;
     case "eliminarEmpresa":
         $resultado = false;
         $bd = new BdEmpresas();
         if(isset($_REQUEST['nombre'])){ //esta parte elimina empresas de la base de datos
             $resultado = $bd->eliminarEmpresa($_REQUEST['nombre']);
         }
         echo json_encode($resultado);
         break;
     case "mostrarEmpresas":
         $bd = new BdEmpresas(); //esta parte devuelve un JSON con los datos de todas las empresas
         $empresas = $bd->mostrarEmpresas();
         echo json_encode($empresas);
         break;
     case "mostrarEmpresasPorNombre":
         $bd = new BdEmpresas(); //esta parte devuelve un JSON con los nombres de todas las empresas
         $empresasPorNombre = $bd->mostrarEmpresasPorNombre();
         echo json_encode($empresasPorNombre);
         break;
     case "mostrarEmpresaPasandoCod":
         if(isset($_REQUEST['cod'])){
             $bd = new BdEmpresas(); //esta parte devuelve un JSON con los datos de la empresa que de la que hemos pasado el cod
             $empresaCod = $bd->mostrarEmpresaPasandoCod($_REQUEST['cod']);
         }
         echo json_encode($empresaCod);
         break;
     case "mostrarCodEmpresaPasandoNombre":
         if(isset($_REQUEST['nombre'])){
             $bd = new BdEmpresas(); //esta parte devuelve un JSON con los datos de la empresa que de la que hemos pasado el cod
             $empresaCod = $bd->mostrarCodEmpresaPansandoNombre($_REQUEST['nombre']);
         }
         echo json_encode($empresaCod);
         break;
     case "modificarEmpresa":
         $resultado = false;// esta parte se encarga de modificar datos de empresas de la base de datos
         if(isset($_REQUEST['nombreViejo']) && isset($_REQUEST['nombre']) && isset($_REQUEST['telefono']) &&
             isset($_REQUEST['provincia']) && isset($_REQUEST['direccion']) && isset($_REQUEST['email'])){
             $bd = new BdEmpresas();
             $resultado = $bd->modificarEmpresa($_REQUEST['nombreViejo'],$_REQUEST['nombre'],$_REQUEST['telefono'],
                          $_REQUEST['provincia'],$_REQUEST['direccion'],$_REQUEST['email']);
         }
         echo json_encode($resultado);
         break;
     /*
      * Gestion de cursos de la base de datos
      */
     case "registrarCurso":
         $resultado = false; //esta parte de la API introduce cursos en la BD
         if(isset($_REQUEST['empresa']) && isset($_REQUEST['fecha']) && isset($_REQUEST['conductor']) && isset($_REQUEST['instructor']) &&
             isset($_REQUEST['ayudante']) && isset($_REQUEST['trayecto']) && isset($_REQUEST['kmIda']) && isset($_REQUEST['horaIda']) &&
             isset($_REQUEST['kmLLegada']) && isset($_REQUEST['horaLlegada'])){
             $bdEmpresa = new BdEmpresas();
             $codEmpresa = $bdEmpresa->mostrarCodEmpresaPansandoNombre($_REQUEST['empresa']);
             $bd = new BdCursos();
             $resultado = $bd->registrarCurso($codEmpresa,$_REQUEST['fecha'],$_REQUEST['conductor'],$_REQUEST['instructor'],
                 $_REQUEST['ayudante'],$_REQUEST['trayecto'],$_REQUEST['kmIda'],$_REQUEST['horaIda'],$_REQUEST['kmLLegada'],
                 $_REQUEST['horaLlegada']);
         }
         echo json_encode($resultado);
         break;
     case "mostrarCursos":
         $bd = new BdCursos(); //esta parte de la API muestra todos los cursos de la base de datos en formato JSON
         $cursos = $bd->mostrarCursos();
         echo json_encode($cursos);
         break;
     case "mostrarCursosPorEmpresa":
        if(isset($_REQUEST['cod'])){
            $bd = new BdCursos(); //esta parte de la API muestra los cursos que se han impartido en una empresa concreta
            $cursosEmpresa = $bd->mostrarCursosPorEmpresa($_REQUEST['cod']);
        }
        echo json_encode($cursosEmpresa);
        break;
	case "mostrarCodigosCursos":
		$bd = new BdCursos();
        $codigos = $bd->mostrarCodigosCursos(); //esta parte de la API muestra los codigos de los cursos del personal de la BD
        echo json_encode($codigos);
        break;
     case "eliminarCursoPorEmpresaYFecha":
         $resultado = false; //esta parte de la API elimina cursos de la BD pasandole el codigo de la empresa y la fecha del curso
         if(isset($_REQUEST['cod']) && isset($_REQUEST['fecha'])){
             $bd = new BdCursos();
             $resultado = $bd->eliminarCursoPorEmpresaYFecha($_REQUEST['cod'],$_REQUEST['fecha']);
         }
         echo json_encode($resultado);
        break;
     case "eliminarCursoPorCurso":
         $resultado = false; //esta parte de la API elimina cursos de la BD pasandole el codigo de la empresa y la fecha del curso
         if(isset($_REQUEST['curso'])){
             $bd = new BdCursos();
             $resultado = $bd->eliminarCursoPorCurso($_REQUEST['curso']);
         }
         echo json_encode($resultado);
         break;
     case "modificarCurso":
         $resultado = false;//esta parte se encarga de la gestion y modificacion decursos
         if(isset($_REQUEST['cursoViejo']) && isset($_REQUEST['empresa']) && isset($_REQUEST['fecha']) && isset($_REQUEST['conductor']) && isset($_REQUEST['instructor']) &&
             isset($_REQUEST['ayudante']) && isset($_REQUEST['trayecto']) && isset($_REQUEST['kmIda']) && isset($_REQUEST['horaIda']) &&
             isset($_REQUEST['kmLLegada']) && isset($_REQUEST['horaLLegada'])){
             $bd = new BdCursos();
             $resultado = $bd->modificarCurso($_REQUEST['cursoViejo'],$_REQUEST['empresa'], $_REQUEST['fecha'],$_REQUEST['conductor'],$_REQUEST['instructor'], $_REQUEST['ayudante'],$_REQUEST['trayecto'],$_REQUEST['kmIda'],$_REQUEST['horaIda'], $_REQUEST['kmLLegada'],$_REQUEST['horaLLegada']);
         }
         echo json_encode($resultado);
         break;
 }
