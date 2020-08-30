<?php
/**
 * clase que crea una conexion a mysql para gestionar la base de datos
 */


class BdPersonal
{
    /**
     * @var mysqli contiene el estado de la conexion
     */
    protected $conexion;

    /**
     * BBDD constructor.
     */
    public function __construct()
    {
        $this->conexion=new mysqli();
        $this->conexion->connect(HOST,US,PW,BBDD);
        $this->conexion->set_charset("utf8");
    }


    /**
     * funcion que valida el DNI y el NIE antes de meterlo en a base de datos
     */
    function validarDNIE($dni){
        $valido = false;
        $dni = strtoupper($dni);
        $letra = substr($dni, -1, 1);
        $numero = substr($dni, 0, 8);
        // Si es un NIE hay que cambiar la primera letra por 0, 1 ó 2 dependiendo de si es X, Y o Z.
        $numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);
        $modulo = $numero % 23;
        $letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
        $letra_correcta = substr($letras_validas, $modulo, 1);

        if($letra_correcta!=$letra) {
           $valido = false;
        }
        else {
            $valido = true;
        }
        return $valido;
    }



    /**
     * Funcion que mediante un parámetro que contiene el dni revisa en la base de datos si el dato existe
     * @param $dni string que contiene un dni
     * @return bool devuelve si existe o no en la base de datos dicho string
     */
    public function comprobarPersonal($dni){
        $resultado = false;
        $consulta = "SELECT * FROM personal WHERE UPPER(dni)='$dni'";
        if($this->conexion->query($consulta)){
            $resultadoConsulta = $this->conexion->query($consulta);
            $row = $resultadoConsulta->fetch_assoc();
            if($row != null){
                $resultado = true;
            }
        }
        unset($this->conexion);
        return $resultado;
    }

    /**
     * funcion que agrega personal a la base de datos
     * @param $dni string que contiene el dni de la persona a agregar
     * @param $nombre string que contiene el nombre de la persona a agregar
     * @param $tipo tipo de persona que es, 1 para instructores 2 para ayudantes
     * @return bool devuelve si se consigue o no guardar los datos
     */
    public function agregarPersonal($dni,$nombre,$telefono,$email,$tipo){
        $resultado = false;
        $resultadoGeneral = false;
        $resultadoParticular = false;
        $nombreArreglado = trim(strip_tags(html_entity_decode($nombre)));
        $dniArreglado = trim(strip_tags(html_entity_decode($dni)));
        $telefonoArreglado = trim(strip_tags(html_entity_decode($telefono)));
        $emailArreglado = trim(strip_tags(html_entity_decode($email)));
        $consultaPersonal = "INSERT INTO personal (dni,nombre,telefono,email) 
                            VALUES ('$dniArreglado','$nombreArreglado','$telefonoArreglado','$emailArreglado')";
        if($this->validarDNIE($dniArreglado)){
            if($this->conexion->query($consultaPersonal)){
                $resultadoGeneral = true;
                switch ($tipo){
                    case 1:
                        if($this->agregarInstructores($dniArreglado,$nombreArreglado)){
                            $resultadoParticular=true;
                        }
                        break;
                    case 2:
                        if($this->agregarAyudantes($dniArreglado,$nombreArreglado)){
                            $resultadoParticular=true;
                        }
                        break;
                }
            }
            if($resultadoParticular && $resultadoGeneral){
                $resultado = true;
            }
            unset($this->conexion);
        }
        return $resultado;

    }

    /**
     * funcion que agrega un ayudante a la base de datos siendo llamada desde la funcion agregarPersonal()
     * @param $dni contiene el string con el dni de la persona
     * @param $nombre contiene el string con el nombre de la persona
     * @return bool devuelve si se guarda o no el ayudante
     */
    public function agregarAyudantes($dni,$nombre){
        $resultado = false;
        $consulta = "INSERT INTO ayudantes (dni,nombre) VALUES ('$dni','$nombre')";
        if($this->conexion->query($consulta)){
            $resultado=true;
        }
        return $resultado;

    }

    /**
     * funcion que agrega un instructor a la base de datos siendo llamada desde la funcion agregarPersonal()
     * @param $dni contiene el string con el dni de la persona
     * @param $nombre contiene el string con el nombre de la persona
     * @return bool devuelve si se guarda o no el instructor
     */
    public function agregarInstructores($dni,$nombre){
        $resultado=false;
        $consulta = "INSERT INTO instructores (dni,nombre) VALUES ('$dni','$nombre')";
        if($this->conexion->query($consulta)){
            $resultado=true;
        }
        return $resultado;

    }

    /**
     * funcion que borra personal de la base de datos mediante la busqueda del dni que se le pasa
     * @param $dni string que contiene el dni a borrar de la base de datos
     * @return bool devuelve si se consigue hacer el borrado
     * @throws Exception
     */
    public function borrarPersonal($dni){
        $resultado = false;
        if($this->comprobarPersonal($dni)){
            self::__construct();
            $consulta = "DELETE FROM personal WHERE UPPER(dni)='$dni'";
            if($this->conexion->query($consulta)){
                $resultado = true;
            }
        }
        unset($this->conexion);
        return $resultado;
    }

    /**
     * funcion que recorre la base de datos y devuelve un JSON con los datos de todos los trabajadores
     * @return false|string
     */
    public function mostrarPersonal(){
        $arrayPersonal = array();
        $consulta = "SELECT * FROM personal";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                $arrayPersonal [] = $row;
            }
        }
        unset($this->conexion);
        return $arrayPersonal;

    }

    /**
     * funcion que recorre la base de datos y devuelve un JSON con los datos de todos los instructores
     * @return false|string
     */
    public function mostrarInstructores(){
        $arrayPersonal = array();
        $consulta = "SELECT * FROM instructores";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                $arrayPersonal[] = $row;
            }
        }
        unset($this->conexion);
        return $arrayPersonal;

    }

    /**
     * funcion que recorre la base de datos y devuelve un JSON con los datos de todos los ayudantes
     * @return false|string
     */
    public function mostrarAyudantes(){
        $arrayPersonal = array();
        $consulta = "SELECT * FROM ayudantes";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                $arrayPersonal[] = $row;
            }
        }
        unset($this->conexion);
        return $arrayPersonal;
    }

    /**
     * Esta funcion muestra los dnis de los ayudantes
     * @return array devuelve un array con los dnis de todos los ayudantes
     */
    public function mostrarDNIAyudantes(){
        $DNIS = array();
        $personal = self::mostrarAyudantes();
        foreach ($personal as $valor){
            foreach ($valor as $key => $valor2){
                if($key == 'dni'){
                    $DNIS[] = $valor2;
                }
            }
        }
        return $DNIS;
    }

    /**
     * Esta funcion muestra los dnis de los ayudantes
     * @return array devuelve un array con los dnis de todos los ayudantes
     */
    public function mostrarDNISInstructores(){
        $DNIS = array();
        $personal = self::mostrarInstructores();
        foreach ($personal as $valor){
            foreach ($valor as $key => $valor2){
                if($key == 'dni'){
                    $DNIS[] = $valor2;
                }
            }
        }
        return $DNIS;
    }


    /**
     * esta funcion cambia el personal de ayudante a instructor en la base de datos
     * @param $dni se le pasa el dni del personal ayudante que quieres cambiar
     * @return bool devuelve si se consigue o no cambiar el empleado de categoria
     */
    public function cambiarAyudanteAInstructor($dni){
        $resultadoPrevio = false;
        $resultadoBorrado = false;
        $resultadoGuardado = false;
        $nombre = array();
        $nombreGuardar = '';
        $consulta = "SELECT nombre FROM ayudantes WHERE dni='$dni'";
        if($this->conexion->query($consulta)){
            $resultadoConsulta = $this->conexion->query($consulta);
            while ($row = $resultadoConsulta->fetch_assoc()){
                $nombre[] = $row;
            }
            foreach ($nombre as $valor){
                foreach ($valor as $key => $valor2){
                    if($key == 'nombre'){
                        $nombreGuardar = $valor2;
                    }
                }
            }
            $resultadoPrevio = true;
        }
        $consultaBorrado = "DELETE FROM ayudantes WHERE dni='$dni'";
        $consultaGuardado = "INSERT INTO instructores values ('$dni','$nombreGuardar')";
        if($resultadoPrevio && $this->conexion->query($consultaBorrado)){
            $resultadoBorrado = true;
        }
        if($resultadoBorrado && $this->conexion->query($consultaGuardado)){
            $resultadoGuardado = true;
        }
        unset($this->conexion);
        return $resultadoGuardado;

    }

    /**
     * esta funcion devuelve un array con todos los dnis del personal registrado en la base de datos
     * @return array devuelve un array de dos dimensiones con los dnis
     */
    public function mostrarDniPersonal(){
        $DNIS = array();
        $personal = self::mostrarPersonal();
        foreach ($personal as $valor){
            foreach ($valor as $key => $valor2){
                if($key == 'dni'){
                    $DNIS[] = $valor2;
                }
            }
        }
        return $DNIS;
    }

    /**
     * esta funcion devuelve un array con todos los nombres del personal registrado en la base de datos
     * @return array devuelve un array de dos dimensiones con los dnis
     */
    public function mostrarNombresPersonal(){
        $nombres = array();
        $personal = self::mostrarPersonal();
        foreach ($personal as $valor){
            foreach ($valor as $key => $valor2){
                if($key == 'nombre'){
                    $nombres[] = $valor2;
                }
            }
        }
        return $nombres;
    }

    /**
     * funcion que modifica un elemento de la tabla personal, en cascada modifica los datos de ese
     * usuario en las tablas instructores, ayudantes, y cursos.
     * @param $dniViejo se le pasa un paramentro con el dni a modificar
     * @param $dni se le pasa el dni nuevo
     * @param $nombre se le pasa un strig con el nombre nuevo
     * @param $telefono string con el telefono nuevo
     * @param $email string con el email
     * @return bool devuelve si se cambian o no los elementos en true o false
     */
    public function modificarPersonal($dniViejo,$dni,$nombre,$telefono,$email){
        $resultado = false;
        $nombreArreglado = trim(strip_tags(html_entity_decode($nombre)));
        $dniArreglado = trim(strip_tags(html_entity_decode($dni)));
        $telefonoArreglado = trim(strip_tags(html_entity_decode($telefono)));
        $emailArreglado = trim(strip_tags(html_entity_decode($email)));
        $consultaPersonal = "UPDATE personal SET dni='$dniArreglado', nombre='$nombreArreglado', telefono='$telefonoArreglado', 
                              email='$emailArreglado' WHERE UPPER(dni)='$dniViejo'";
        if($this->validarDNIE($dniArreglado)){
            if($this->conexion->query($consultaPersonal)){
                $resultado = true;
            }
        }
        unset($this->conexion);
        return $resultado;

    }
}

