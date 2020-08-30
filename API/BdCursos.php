<?php
/**
 * clase que gestiona la base de datos que contiene los cursos y sus datos
 * Class BdCursos
 */
class BdCursos
{

    /**
     * parametro que va a contener los datos de la conexion
     * @var mysqli
     */

    protected $conexion;

    /**
     * constructor que inicia la conexion a la base de datos
     * BdCursos constructor.
     */
    public function __construct()
    {
        $this->conexion=new mysqli();
        $this->conexion->connect(HOST,US,PW,BBDD);
        $this->conexion->set_charset("utf8");
    }

    /**
     * funcion que revisa el ultimo cod registrado y lo aumenta para guardar correlativamente los cursos
     * @return int
     */
    public function comprobarCod(){
        $consulta = "SELECT MAX(curso) from cursos";
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
     * funcion que registra en la base de datos un nuevo curso y su trayecto
     * @param $empresa string que contiene el nombre de la empresa en la que se realiza el curso
     * @param $conductor string que contiene el dni del conductor
     * @param $instructor string que contiene el dni del instructor
     * @param $ayudante string que contiene el dni del ayudante
     * @param $trayecto string que contiene si el trayecto de ida o de vuelta
     * @param $kmIda string que contiene los km que tenia el vehiculo al salir
     * @param $horaIda  string que contiene la hora a la que comienza el viaje
     * @param $kmLLegada string que contiene los km del vehiculo al llegar
     * @param $horaLlegada string que contiene la hora a la que se llega al destino
     * @return bool devuelve si se consigue o no guardar en la base de datos
     */
    public function registrarCurso($codEmpresa,$fecha,$conductor,$instructor,$ayudante,$trayecto,$kmIda,$horaIda,$kmLLegada,$horaLlegada){

        $cod = self::comprobarCod();
        $cod++;
        self::__construct();
        $resultado = false;
        $conductorArreglado = trim(strip_tags(html_entity_decode($conductor)));
        $instructorArreglado = trim(strip_tags(html_entity_decode($instructor)));
        $ayudanteArreglado = trim(strip_tags(html_entity_decode($ayudante)));
        $trayectoArreglado = trim(strip_tags(html_entity_decode($trayecto)));
        $kmIdaArreglado = trim(strip_tags(html_entity_decode($kmIda)));
        $horaIdaArreglado = trim(strip_tags(html_entity_decode($horaIda)));
        $kmLLegadaArreglado = trim(strip_tags(html_entity_decode($kmLLegada)));
        $horaLlegadaArreglado = trim(strip_tags(html_entity_decode($horaLlegada)));
        $consulta = "INSERT INTO cursos VALUES ('$cod','$codEmpresa','$fecha','$conductorArreglado','$instructorArreglado','$ayudanteArreglado','$trayectoArreglado','$kmIdaArreglado','$horaIdaArreglado','$kmLLegadaArreglado','$horaLlegadaArreglado')";
        if($this->conexion->query($consulta)){
            $resultado = true;
        }
        unset($this->conexion);
        return $resultado;
    }

    /**
     * funcion que devuelve todos los cursos realizados
     * @return array devuelve un array con todos los datos de los cursos
     */
  public function mostrarCursos(){
        $arrayCursos = array();
        $consulta = "SELECT C.curso AS curso, E.nombre AS empresa,C.fecha AS fecha, C.conductor AS conductor, 
                        C.instructor AS instructor, C.ayudante AS ayudante, C.trayecto AS trayecto, C.kmida AS kmida, 
                        C.horaida AS horaida, C.kmllegada AS kmllegada, C.horallegada AS horallegada FROM cursos C, 
                        empresas E WHERE C.empresa = E.cod ";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                $arrayCursos[] = $row;
            }
        }
        unset($this->conexion);
        return $arrayCursos;
    }


    /**
     * funcion que devuelve los cursos realziados en una empresa concreta
     * @param $cod se le pasa un cod para que busque esa empresa en la base de datos
     * @return array devuelve un array con los datos de la empresa seleccionada
     */
    public function mostrarCursosPorEmpresa($cod){
        $arrayCursos = array();
        $cont = 0;
        $curso = array();
        $consulta = "SELECT * FROM cursos WHERE empresa='$cod'";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                foreach ($row as $tipo => $valor){
                    $curso[$tipo] = $valor;
                }
                $arrayCursos[$cont] = $curso;
                $cont++;
            }
        }
        unset($this->conexion);
        return $arrayCursos;
    }
	
	/**
     * funcion que devuelve los cursos realziados por su codigo
     * @return array devuelve un array con los codigos de los cursos
     */
    public function mostrarCodigosCursos(){
        $codigos = array();
        $cursos = self::mostrarCursos();
        foreach ($cursos as $valor){
            foreach ($valor as $key => $valor2){
                if($key == 'curso'){
                    $codigos[] = $valor2;
                }
            }
        }
        return $codigos;
    }

    /**
     * esta funcion elimina cursos de la base de datos teniendo en cuenta el codigo del curso y la fecha en que se realizo
     * @param $cod se le pasa un cod que contiene la empresa
     * @param $fecha se le pasa una fecha del curso realizado a borrar
     * @return bool devuelve si se consigue o no realizar el borrado
     */
    public function eliminarCursoPorEmpresaYFecha($cod,$fecha){
        $resultado = false;
        $consulta = "DELETE FROM cursos WHERE curso='$cod' AND fecha='$fecha'";
        if($this->conexion->query($consulta)){
            $resultado = true;
        }
        unset($this->conexion);
        return $resultado;
    }

    /**
     * esta funcion elimina cursos de la base de datos teniendo en cuenta el codigo del curso y la fecha en que se realizo
     * @param $curso se le pasa un cod que contiene la empresa
     * @return bool devuelve si se consigue o no realizar el borrado
     */
    public function eliminarCursoPorCurso($curso){
        $resultado = false;
        $consulta = "DELETE FROM cursos WHERE curso='$curso'";
        if($this->conexion->query($consulta)){
            $resultado = true;
        }
        unset($this->conexion);
        return $resultado;
    }

    /**
     * esta funcion modifica los cursos que hay guardados en la base de datos
     * @param $cursoVejo string que cpontiene el codigo del curso a modificar
     * @param $codEmpresa string que contine el codigo de la empresa en la que se da el curso
     * @param $fecha fecha del curso
     * @param $conductor string con el dni del conductor
     * @param $instructor string con el dni del instructor
     * @param $ayudante string con el dni del ayudante
     * @param $trayecto string con el trayecto ida o vuelta
     * @param $kmIda string ocn lso km de l vehicilp a al salida
     * @param $horaIda string con la hora de la salida
     * @param $kmLLegada string con los km del vehiculo a la llegada
     * @param $horaLlegada string con ñla hora  de la llegada
     * @return bool devuelve si se consuigue o no el cambio de datos
     */
    public function modificarCurso($cursoVejo,$empresa,$fecha,$conductor,$instructor,$ayudante,$trayecto,$kmIda,$horaIda,$kmLLegada,$horaLlegada){
        $resultado = false;
        $codigo = self::mostrarCodEmpresaPasandoNombre($empresa);//aqui cambiamos el  nombre de la empresa por el codigo
        self::__construct();//construimos de nuevo una conexion
        $conductorArreglado = trim(strip_tags(html_entity_decode($conductor)));
        $instructorArreglado = trim(strip_tags(html_entity_decode($instructor)));
        $ayudanteArreglado = trim(strip_tags(html_entity_decode($ayudante)));
        $trayectoArreglado = trim(strip_tags(html_entity_decode($trayecto)));
        $kmIdaArreglado = trim(strip_tags(html_entity_decode($kmIda)));
        $horaIdaArreglado = trim(strip_tags(html_entity_decode($horaIda)));
        $kmLLegadaArreglado = trim(strip_tags(html_entity_decode($kmLLegada)));
        $horaLlegadaArreglado = trim(strip_tags(html_entity_decode($horaLlegada)));
        $consulta = "UPDATE cursos SET empresa='$codigo', fecha='$fecha', conductor='$conductorArreglado', instructor='$instructorArreglado',".
                    "ayudante='$ayudanteArreglado', trayecto='$trayectoArreglado', kmida='$kmIdaArreglado', horaida ='$horaIdaArreglado',".
                     "kmllegada='$kmLLegadaArreglado', horallegada='$horaLlegadaArreglado' WHERE curso='$cursoVejo'";
        if($this->conexion->query($consulta)){
            $resultado = true;
        }
        unset($this->conexion);
        return $resultado;
    }

    /**esta funcion recibe el nombre de una empresa y busca su codigo en la base de datos devolviendolo
     * @param $nombre string que contiene el nombre de la empresa a buscar en la BD
     * @return mixed|string devuelve un string con el numero de cod de dicha empresa
     */
    public function mostrarCodEmpresaPasandoNombre($nombre){
        $resultado = "";
        $codigo = "";
        $consulta = "SELECT cod FROM empresas WHERE UPPER(nombre)=UPPER('$nombre')";
        if($this->conexion->query($consulta)){
            $resultado = $this->conexion->query($consulta);
            while ($row = $resultado->fetch_assoc()){
                foreach ($row as $valor){
                    $codigo = $valor;
                }
            }
        }
        unset($this->conexion);
        return $codigo;
    }
}