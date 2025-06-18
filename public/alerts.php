<?php
if (!empty($_SESSION['logout_message'])) {
    echo mostrar_mensaje_logout();
    echo '<script>
            jQuery(document).ready(function () {
                jQuery("#logoutModal").modal("show");
            });
          </script>';
    unset($_SESSION['logout_message']);
}

if (!empty($_SESSION['login_message']) && $_SESSION['login_message'] === 3) {
    echo mostrar_mensaje_login();
    echo '<script>
            jQuery(document).ready(function () {
                jQuery("#loginModal").modal("show");
            });
          </script>';
    unset($_SESSION['login_message']);
}

$mensajeEvento = mostrar_mensaje_evento();

if (!empty($_SESSION['event_message'])) {
    echo '<script>
            ' . $mensajeEvento . ';
            setTimeout(function () {
                window.location.href = "novedades.php";
            }, 5000);
          </script>';
    unset($_SESSION['event_message']);
}

$scriptEvento = "
    <script>
        $('#confirmModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let eventID = button.data('id');
            let deleteLink = $('#deleteEventLink');
            deleteLink.attr('href', 'novedades.php?eventID=' + eventID);
        });

        $('#updateModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let editID_evento = button.data('id');
            let editModal = $('#editID_evento');
            editModal.attr('value', editID_evento);
        });
    </script>
";

if (str_contains($_SERVER['REQUEST_URI'], 'novedades.php')) {
    echo $scriptEvento;
}
