<?php
session_start();
require_once("funzioni.php");
//verifico l' esistenza di una sessione gia' avviata
if (isset($_SESSION['session_id'])) {
    $session_nome = htmlspecialchars($_SESSION['session_nome'], ENT_QUOTES, 'UTF-8');
    //creo la pagina
    echo creaInizioPagina();
    echo creaMenuNavbar();

    if ($_GET["op"] == 'MEN') {
        echo ('<div class="paginaDashboard">');
        echo ('<div class="containerTitoloDashboard">');
        echo ('<h3>Benvenuto ' . $session_nome . '</h3>');
        echo ('</div>');
        echo ('<div class="containerIconsDashboard">');
        echo ('<div class="containerUsers">');
        echo ('<a href="./users.php?op=VIS&idSel=""" title="Visualizza utenti"><img src="./img/users.png"><br>Utenti</a>');
        echo ('</div>');
        echo ('<div class="containerLavori">');
        echo ('<a href="./lavori.php?op=VIS" title="Visualizza lavor"><img src="./img/content-management.png"><br>Lavori</a>');
        echo ('</div>');
        echo ('</div>');
        echo ('<div class="containerLogoutDashboard">');
        echo ('<div onclick="fnLogout()">Logout</div>');
        echo ('</div>');
        echo ('</div>');
        //se viene premuto il pulsant di LOGOUT
    } else if ($_GET["op"] == "LOGOUT") {
        unset($_SESSION['session_id']);
        echo '<script>
            window.onload = function() {
             window.location.replace("/login.php?op=LOG");
         } </script>';
    }
    echo creaFinePagina();
} else {
    //Se non è stato effettuato il login faccio apparire il messaggio di errore
    echo creaInizioPagina();
    echo creaMenuNavbar();
    echo ('<div class="paginaDashboard">');
    echo ('<div class="containerDashboard">');
    echo ('<div class="noLog">Effettua il  <a href="./login.php?op=LOG">login</a> per accdere all\'area riservata.</div>');
    echo ('</div>');
    echo ('</div>');
    echo creaFinePagina();
}
