<?php
const DB_HOST = 'proyecto-db-1';
 const DB_NAME = 'eventos';
const DB_PORT = '3306';
const DB_USER = 'user1';
const DB_PASS = 'user1.pa55';

class Conexion
{
    private PDO $conexion;

    public function conectar(): PDO
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT;
            $this->conexion = new PDO($dsn, DB_USER, DB_PASS);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch (PDOException $e) {
            echo 'Error al conectar a la base de datos: ' . $e->getMessage();
            exit();
        }
    }
}
