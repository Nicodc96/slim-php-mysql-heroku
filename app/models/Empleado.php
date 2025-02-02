<?php
require "./models/Pedido.php";
require "./db/AccesoDatos.php";

class Empleado{
    public $id;
    public $usuario_id;
    public $id_area_empleado;
    public $nombre;
    public $fecha_alta;
    public $fecha_baja;

    public function __construct(){}

    public static function crearEmpleado($usuario_id, $id_area_empleado, $nombre, $fecha_alta){
        $empleado = new Empleado();
        $empleado->usuario_id = $usuario_id;
        $empleado->id_area_empleado = $id_area_empleado;
        $empleado->nombre = $nombre;
        $empleado->fecha_alta = $fecha_alta;
        return $empleado;
    }

    public static function insertarEmpleadoDB($empleado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (usuario_id, id_area_empleado, nombre, fecha_alta)
        VALUES (:usuario_id, :id_area_empleado, :nombre, :fecha_alta);");
        $consulta->bindParam(":usuario_id", $empleado->usuario_id);
        $consulta->bindParam(":id_area_empleado", $empleado->id_area_empleado);
        $consulta->bindParam(":nombre", $empleado->nombre);
        $consulta->bindParam(":fecha_alta", $empleado->fecha_alta);
        try {
            $consulta->execute();
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }        
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
    }

    public static function mostrarEmpleadosTabla($array_empleados = array()){
        if (count($array_empleados) <= 0){
            $arrayEmpleados = self::obtenerTodos();
        }
        $mensaje = "Lista vacia.";
        if (is_array($arrayEmpleados) && count($arrayEmpleados) > 0){
            $mensaje = "<h3 align='center'> Lista de Empleados </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Usuario ID</th><th>Nombre</th><th>ID Area Trabajo</th><th>Fecha Alta</th><th>Fecha Baja</th></tr><tbody>";
            foreach($arrayEmpleados as $empleado){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $empleado->id . "</td>" .
                "<td>" . $empleado->usuario_id . "</td>" .
                "<td>" . $empleado->nombre . "</td>" .
                "<td>" . $empleado->id_area_empleado . "</td>" .
                "<td>" . $empleado->fecha_alta . "</td>" .
                "<td>" . $empleado->fecha_baja . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function mostrarEmpleadoTabla($empleado){
        $mensaje = "El objeto enviado por parámetro no es un empleado.";
        if (is_a($empleado, "Empleado")){
            $mensaje = "<h3 align='center'> Lista de Empleados </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Usuario ID</th><th>Nombre</th><th>ID Area Trabajo</th><th>Fecha Alta</th><th>Fecha Baja</th></tr><tbody>";
            $mensaje .= "<tr align='center'>" .
            "<td>" . $empleado->id . "</td>" .
            "<td>" . $empleado->usuario_id . "</td>" .
            "<td>" . $empleado->nombre . "</td>" .
            "<td>" . $empleado->id_area_empleado . "</td>" .
            "<td>" . $empleado->fecha_alta . "</td>" .
            "<td>" . $empleado->fecha_baja . "</td></tr>";
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function obtenerEmpleadoPorId($empleadoId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados WHERE id = :id");
        $consulta->bindValue(":id", $empleadoId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject("Empleado");
    }

    public static function modificarEmpleado($empleadoParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET usuario_id = :usuario_id, id_area_empleado = :id_area_empleado, nombre = :nombre
        WHERE id = :id");
        try{
            $consulta->bindValue(":usuario_id", $empleadoParam->usuario_id, PDO::PARAM_INT);
            $consulta->bindValue(":id_area_empleado", $empleadoParam->id_area_empleado, PDO::PARAM_INT);
            $consulta->bindValue(":nombre", $empleadoParam->nombre, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }
    public static function borrarEmpleado($empleadoId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET fecha_baja = :fechaBaja WHERE id = :id");
        try{
            $fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(":id", $empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(":fechaBaja", date_format($fecha, "Y-m-d H:i:s"));
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }
}
?>