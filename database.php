<?php
require_once('dati.php');


try {
    $pdo = new PDO("mysql:host=".INDIRIZZO.";dbname=" . DB,UTENTE,PASSWORD);
        
} catch (PDOException $e) {
    exit("<br><br>Errore PDO: " . $e->getMessage());
}?>