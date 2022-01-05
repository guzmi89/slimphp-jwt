<?php
//Añado la clase de la DB.
require __DIR__ . '/../config/Db.php';

//Devuelve los resultados de una consulta
function getConnection($sql, $params = []) {
    $conexion           = new Db;
    $resultado          = $conexion->query($sql, $params);
    return $resultado;
}
?>