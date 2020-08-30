<?php

/**
 * clase que gestiona la base de datos que contiene las empresas y sus datos
 * Class BdEmpresas
 */
class BdEmpresas
{

    /**
     * parametro que va a contener los datos de la conexion
     * @var mysqli
     */
    protected $conexion;

    /**
     * constructor que inicia la conexion a la base de datos
     * BdEmpresas constructor.
     */
    public function __construct()
    {
        $this->conexion=new mysqli();
        $this->conexion->connect(HOST,US,PW,BBDD);
        $this->conexion->set_charset("utf8");
    }

    /**
     * funcion que revisa el ultimo cod registrado y lo aumenta para guardar correlativamente las empresas
     * @return int
     */
    public function comprobarCod(){
        $consulta = "SELECT MAX(cod) from empresas";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            $row = $resultado->fetch_assoc();
            foreach ($row as $valor){
                $numero = $valor;
            }
        }
        unset($this->conexion);
        if($numero == null){
            $numero = 0;
        }
        return $numero;
    }

    /**
     * funcion que guarda en la base de datos las empresas
     * @param $nombre string que contiene el nombre de la empresa
     * @param $telefono sgtring que contiene el numero de telefono
     * @param $provincia string que contiene la provincia
     * @param $direccion string que contiene la direccion de la empresa
     * @param $email string que contiene que email
     * @return bool devuelve si se consigue o no guardar la empresa en a BD
     */
    public function introducirEmpresa($nombre,$telefono,$provincia,$direccion,$email){
        $resultado = false;
        $cod = self::comprobarCod();
        $cod++;
        self::__construct();
        $nombreArreglado = trim(strip_tags(html_entity_decode($nombre)));
        $provinciaArreglado = trim(strip_tags(html_entity_decode($provincia)));
        $telefonoArreglado = trim(strip_tags(html_entity_decode($telefono)));
        $direccionArreglado = trim(strip_tags(html_entity_decode($direccion)));
        $emailArreglado = trim(strip_tags(html_entity_decode($email)));
        $consulta = "INSERT INTO empresas VALUES ('$cod','$nombreArreglado','$telefonoArreglado','$provinciaArreglado','$direccionArreglado','$emailArreglado')";
        if($this->conexion->query($consulta)){
            $resultado = true;
        }
        unset($this->conexion);
        return $resultado;

    }

    /**
     * funcion que elimina una empresa de la base de datos teniendo en cuenta el nombre
     * @param $nombre string que contiene el nombre de la empresa a eliminar
     * @return bool devuelve si se borra o no la empresa de la BD
     */
    public function eliminarEmpresa($nombre){
        $resultado = false;
        $consulta = "DELETE FROM empresas WHERE UPPER(nombre)='$nombre'";
        $this->conexion->query($consulta) ? $resultado = true : $resultado = false;
        unset($this->conexion);
        return $resultado;
    }

    /**
     * funcion que muestra todas las empresas guardadas en la base de datos
     * @return false|string devuelve un JSON para ser recibido en la API
     */
    public function mostrarEmpresas(){
        $arrayEmpresas = array();
        $consulta = "SELECT * FROM empresas";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                $arrayEmpresas[] = $row;
            }
        }
        unset($this->conexion);
        return $arrayEmpresas;
    }

    /**
     * funcion que devielve unicamente los nombres de las empresas de la base de datos
     * @return false|string devuelve un JSON con todos los nombres de empresas
     */
    public function mostrarEmpresasPorNombre(){
        $nombres = array();
        $empresas = self::mostrarEmpresas();
        foreach ($empresas as $valor){
            foreach ($valor as $key => $valor2){
                if($key == 'nombre'){
                    $nombres[] = $valor2;
                }
            }
        }
        return $nombres;
    }

    /**
     * funcion que muestra los datos de una empresa pasandole el cod
     * @param $cod contiene un int con el numero de la empresa
     * @return array devuelve un array con todos los datos de la empresa
     */
    public function mostrarEmpresaPasandoCod($cod){
        $empresa = array();
        $consulta = "SELECT * FROM empresas WHERE cod='$cod'";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                foreach ($row as $key => $valor){
                    $empresa[$key] = $valor;
                }
            }
        }
        unset($this->conexion);
        return $empresa;
    }

    /**
     * Esta funcion muestra el cod de la empresa pasandole primero el nombre que se busca
     * @param $nombre string qiue contiene el  noimbre de la empresa
     * @return mixed|string devuelve un string con el codigo de la base de datos si es que existe
     */
    public function mostrarCodEmpresaPansandoNombre($nombre){
        $consulta = "SELECT cod FROM empresas WHERE UPPER(nombre)=UPPER('$nombre')";
        $empresa = "";
        $codigo = "";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while($row = $resultado->fetch_assoc()){
                $empresa = $row;
            }
            foreach ($empresa as $valor){
                $codigo = $valor;
            }
        }
        unset($this->conexion);
        return $codigo;

    }

    /**
     * Esta funcion se encarga de modificar datos de empresas que ya existen en la base de datos
     * @param $nombreViejo string que contiene el nombre de la empresa a cambiar
     * @param $nombre string que contiene el nombre nuevo que se le va a signar
     * @param $telefono string que contiene el numero de telefono de la empresa
     * @param $provincia string que contiene la provincia de la empresa
     * @param $direccion string que contiene la direccion de la empresa
     * @param $email string que contiene el email de la empresa
     * @return bool devuelve true si se consigue hacer el cambio false si no lo hace
     */
    public function modificarEmpresa($nombreViejo,$nombre,$telefono,$provincia,$direccion,$email){
        $resultado = false;
        $nombreArreglado = trim(strip_tags(html_entity_decode($nombre)));
        $provinciaArreglado = trim(strip_tags(html_entity_decode($provincia)));
        $telefonoArreglado = trim(strip_tags(html_entity_decode($telefono)));
        $direccionArreglado = trim(strip_tags(html_entity_decode($direccion)));
        $emailArreglado = trim(strip_tags(html_entity_decode($email)));
        $consultaModificar = "UPDATE empresas SET nombre='$nombreArreglado', telefono='$telefonoArreglado', 
                            provincia='$provinciaArreglado', direccion='$direccionArreglado', 
                            email='$emailArreglado' WHERE UPPER(nombre)=UPPER('$nombreViejo')";
        if($this->conexion->query($consultaModificar)){
            $resultado = true;
        }
        unset($this->conexion);
        return $resultado;

    }

}