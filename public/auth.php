<?php
require_once 'conexion.php';

class Auth
{
    protected PDO $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion->conectar();
    }

    public function verificar_correo(string $correo): bool
    {
        $sqlVerificarCorreo = $this->conexion->prepare('SELECT ID_Usuario, Nombre, Apellido, Correo, Clave, Cod_Usuario FROM usuarios WHERE Correo = :correo');
        $sqlVerificarCorreo->bindParam(':correo', $correo, PDO::PARAM_STR);
        $sqlVerificarCorreo->execute();
        return $sqlVerificarCorreo->fetch() !== false;
    }

    public function registrar_usuario($nombre, $apellido, $correo, $clave): bool
    {
        $check_stmt = $this->verificar_correo($correo);

        if ($check_stmt === true) {
            $_SESSION['register_message'] = 2;
            return false;
        } else {
            $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

            $stmt = $this->conexion->prepare('INSERT INTO usuarios (Nombre, Apellido, Correo, Clave) VALUES (:nombre, :apellido, :correo, :clave_encriptada)');
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':clave_encriptada', $clave_encriptada, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['register_message'] = 3;
                return true;
            } else {
                $_SESSION['register_message'] = 1;
                return false;
            }
        }
    }

    public function logear_usuario($correo, $clave): bool
    {
        $stmt = $this->verificar_correo($correo);

        if ($stmt === false) {
            $_SESSION['login_message'] = 1;
            return false;
        } else {
            $stmt = $this->conexion->prepare('SELECT * FROM usuarios WHERE Correo = :correo');
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $clave_encriptada = $usuario['Clave'];
            if (!password_verify($clave, $clave_encriptada)) {
                $_SESSION['login_message'] = 2;
                return false;
            } elseif (password_verify($clave, $clave_encriptada)) {
                $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
                $_SESSION['nombre'] = $usuario['Nombre'];
                $_SESSION['apellido'] = $usuario['Apellido'];
                $_SESSION['correo'] = $usuario['Correo'];
                $_SESSION['login_message'] = 3;
                return true;
            } else {
                $_SESSION['login_message'] = 4;
                return false;
            }
        }
    }

    public function eliminar_usuario($correo): bool
    {
        $stmt = $this->conexion->prepare('DELETE FROM usuarios WHERE Correo = :correo');
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
