<?php
require_once 'helpers.php'; // Archivo que contiene los mensajes de alerta

try {
    $pdo = obtenerConexion();

    $lista_eventos = $pdo->query('SELECT * FROM eventos');

    if (isset($_GET['eventID'])) {
        $Eliminar = moverYEliminarEvento($pdo, $_GET, $_GET['eventID']);
        $_SESSION['event_message'] = ($Eliminar ? 4 : 1);
    }

    if (isset($_POST['actualizar_evento'])) {
        $Actualizar = actualizarEvento($pdo, $_POST);
        $_SESSION['event_message'] = ($Actualizar ? 3 : 1);
    }

    if (isset($_POST['agregar_evento'])) {
        $Agregar = agregarEvento($pdo, $_POST);
        $_SESSION['event_message'] = ($Agregar ? 2 : 1);
    }
} catch (PDOException $e) {
    logPDOException($e, 'Descripción de la excepción: ');
} finally {
    $pdo = null;
}

function moverYEliminarEvento(PDO $pdo, array $getData, string $eventID): bool
{
    $eventID = $getData['eventID'];

    try {
        $pdo->beginTransaction();

        $sqlObtenerEvento = $pdo->prepare('SELECT * FROM eventos WHERE ID_Evento = :ID_evento');
        $sqlObtenerEvento->bindParam(':ID_evento', $eventID, PDO::PARAM_INT);
        $sqlObtenerEvento->execute();
        $evento = $sqlObtenerEvento->fetch(PDO::FETCH_ASSOC);

        $sqlMoverEvento = $pdo->prepare('INSERT INTO eventos_eliminados (ID_Evento, Nombre_Evento, Descripcion_Evento, Lugar, Fecha_Y_Hora) VALUES (:ID_evento, :nombre, :descripcion, :lugar, :fecha_hora)');
        $sqlMoverEvento->execute([
            ':ID_evento' => $evento['ID_Evento'],
            ':nombre' => $evento['Nombre_Evento'],
            ':descripcion' => $evento['Descripcion_Evento'],
            ':lugar' => $evento['Lugar'],
            ':fecha_hora' => $evento['Fecha_Y_Hora'],
        ]);

        $sqlEliminarEvento = $pdo->prepare('DELETE FROM eventos WHERE ID_Evento = :ID_evento');
        $sqlEliminarEvento->bindParam(':ID_evento', $eventID, PDO::PARAM_INT);
        $sqlEliminarEvento->execute();

        $pdo->commit();
        return true;
    } catch (Exception) {
        $pdo->rollBack();
        return false;
    }
}

function actualizarEvento(PDO $pdo, array $postData): bool
{
    $sqlActualizarEvento = $pdo->prepare('UPDATE eventos SET Nombre_evento = :nombre, Descripcion_Evento = :descripcion, Lugar = :lugar, Fecha_Y_Hora = :fecha_hora WHERE ID_Evento = :ID_evento');

    $sqlActualizarEvento->bindParam(':ID_evento', $postData['editID_evento'], PDO::PARAM_INT);
    $sqlActualizarEvento->bindParam(':nombre', $postData['editNombre'], PDO::PARAM_STR);
    $sqlActualizarEvento->bindParam(':descripcion', $postData['editDescripcion'], PDO::PARAM_STR);
    $sqlActualizarEvento->bindParam(':lugar', $postData['editLugar'], PDO::PARAM_STR);
    $sqlActualizarEvento->bindParam(':fecha_hora', $postData['editFecha_hora'], PDO::PARAM_STR);

    return $sqlActualizarEvento->execute();
}

function agregarEvento(PDO $pdo, array $postData): bool
{
    $sqlAgregarEvento = $pdo->prepare('INSERT INTO eventos (Nombre_evento, Descripcion_Evento, Lugar, Fecha_Y_Hora) VALUES (:nombre, :descripcion, :lugar, :fecha_hora)');
    $sqlAgregarEvento->bindParam(':nombre', $postData['nombre'], PDO::PARAM_STR);
    $sqlAgregarEvento->bindParam(':descripcion', $postData['descripcion'], PDO::PARAM_STR);
    $sqlAgregarEvento->bindParam(':lugar', $postData['lugar'], PDO::PARAM_STR);
    $sqlAgregarEvento->bindParam(':fecha_hora', $postData['fecha_hora'], PDO::PARAM_STR);

    return $sqlAgregarEvento->execute();
}

function customSort($a, $b): int
{
    $a = strtolower($a);
    $b = strtolower($b);

    return strnatcasecmp($a, $b);
}

function obtenerEventosConOrden($pdo, $ordenamiento)
{
    $ordenamientos = [
        'ID_Evento ASC',
        'Nombre_Evento ASC',
        'Descripcion_Evento ASC',
        'Fecha_De_Registro DESC',
        'Lugar ASC',
        'Fecha_Y_Hora DESC',
    ];

    if (!in_array($ordenamiento, $ordenamientos)) {
        $ordenamiento = 'ID_Evento ASC';
    }

    list($campo, $direccion) = explode(' ', $ordenamiento);

    $sql = "SELECT * FROM eventos ORDER BY $campo $direccion";

    $stmt = $pdo->query($sql);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($eventos)) {
        usort($eventos, function ($a, $b) use ($campo) {
            return customSort($a[$campo], $b[$campo]);
        });
    }

    return $eventos;
}

function generarTablaEventos($lista_eventos): string
{
    ob_start();

    if (!empty($lista_eventos)) {
        foreach ($lista_eventos as $row) {
            echo '<tr>';
            echo '<td class="text-center">' . $row['ID_Evento'] . '</td>';
            echo '<td class="td-scroll">' . htmlspecialchars($row['Nombre_Evento']) . '</td>';
            echo '<td class="td-scroll">' . htmlspecialchars($row['Descripcion_Evento']) . '</td>';
            echo '<td>' . date('d/m/y H:i', strtotime($row['Fecha_De_Registro'])) . '</td>';
            echo '<td class="td-scroll">' . htmlspecialchars($row['Lugar']) . '</td>';
            echo '<td>' . date('d/m/y H:i', strtotime($row['Fecha_Y_Hora'])) . '</td>';
            echo '<td>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal" data-id="' . $row['ID_Evento'] . '">Editar</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="' . $row['ID_Evento'] . '">Eliminar</button>
                </td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='7'>No hay registros</td></tr>";
    }

    return ob_get_clean();
}

if (isset($_POST['ordenarEventos'])) {
    $ordenarEventos = $_POST['ordenarEventos'];

    $pdo = obtenerConexion();

    $ordenamientos = [
        'id-asc' => 'ID_Evento ASC',
        'nombre-asc' => 'Nombre_Evento ASC',
        'descripcion-asc' => 'Descripcion_Evento ASC',
        'fecha-registro-desc' => 'Fecha_De_Registro DESC',
        'lugar-asc' => 'Lugar ASC',
        'fecha-hora-desc' => 'Fecha_Y_Hora DESC',
    ];

    $ordenamiento = $ordenamientos[$ordenarEventos] ?? 'ID_Evento ASC';

    $eventos = obtenerEventosConOrden($pdo, $ordenamiento);

    echo generarTablaEventos($eventos);
    exit();
}
