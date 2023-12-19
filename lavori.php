<?php
session_start();
//Verifico l' esistenza di una sessione gia' avviata
if (isset($_SESSION['session_id'])) {
    //Acquisisco le informazioni sul tipo di utente
    $session_privilegi = htmlspecialchars($_SESSION['session_privilegi'], ENT_QUOTES, 'UTF-8');
    require_once("funzioni.php");
    require_once("dati.php");
    //creo la pagina
    echo creaInizioPagina();
    echo creaMenuNavbar();
    $dati = [];
    $datiLavori = [];
    $menu = '';
    //Acquisisco i parametri della query string
    if (isset($_GET["op"])) {
        $op = htmlspecialchars($_GET['op']);
    } else {
        $op = '';
    }
    if (isset($_GET["idSel"])) {
        $idSel = htmlspecialchars($_GET['idSel']);
    } else {
        $idSel = null;
    }
    //Apro la connessione
    try {
        $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
        $sql = "SELECT id,
                nome,
                descrizione,
                immagine
                FROM db39opf4ki7vt9.lavori;";
        $query = $pdo->prepare($sql);
        $query->execute();
        if ($query->rowCount() > 0) {
            while ($righe = $query->fetch(PDO::FETCH_ASSOC)) {
                $tmp = array(
                    "id" => $righe["id"],
                    "nome" => $righe["nome"],
                    "descrizione" => $righe["descrizione"],
                    "immagine" => $righe["immagine"],
                );
                $dati[] = $tmp;

                if ($idSel != null && $idSel == $tmp["id"]) {
                    $datiLavori = $tmp;
                }
            }
            //Se non è stato selezionato alcun elemento, viene selezionato il primo dell' elenco
            if ($idSel == null) {
                $idSel = $dati[0]["id"];
                $datiLavori = $dati[0];
            }
        }
    } catch (PDOException $e) {
        echo "<br><br>Errore PDO: " . $e->getMessage();
    }
    //**************************** CREO HTML ***********************************

    ///////////////// PAGINA VISUALIZZAZIONE LAVORI //////////////////////////////
    if ($op == "VIS") {
        //Creo la pagina
        echo ('<body>');
        echo ('<div class="paginaVisualizzazioneLavori">');
        echo '<div class="containerTitolo">';
        echo '<a href="/dashboard.php?op=MEN" class="return" title="Torna indietro" style="text-decoration:none;">< Indietro</a>';
        echo '<h1>Elenco lavori</h1>';
        echo '</div>';
        echo ('<div class="flex-container">');
        echo ('<div class="listaLavori">');
        echo creaElencoLavori($dati, $idSel);
        echo ('</div>');
        echo ('<div class="contenuto">');
        echo creaTitoloLavori($datiLavori);
        echo creaFormVisualizzazioneLavori($datiLavori);
        //Se l' utente ha i privilegi permetto la visualizzazione dei pulsanti di modifica
        echo ('<div class= "containerImmagine">');
        if (file_exists('./uploads/' . $datiLavori["id"] . '/' . $datiLavori["immagine"])) {
            echo '<img src="./uploads/' . $datiLavori["id"] . '/' . $datiLavori["immagine"] . '" alt="' . $datiLavori["nome"] . '"></img >';
        } else {
            echo '<img src="./img/default-image.png" alt="' . $datiLavori["nome"] . '"></img >';
        }
        echo ('</div>');
        echo ('</div>');

        if ($session_privilegi == 1) {
            echo ('<div class="containerPulsanti">');
            echo creaPulsantiOperazioni();
            echo ('</div>');
        }
        echo ('</div>');
    }
    /////////////// PAGINA MODIFICA LAVORI //////////////////////////////////////////////
    else if ($op == 'MOD') {

        $nomeErr = $success = "";
        //se viene premuto il tasto conferma
        if (isset($_POST['conferma'])) {
            $datiLavori = array_replace($datiLavori, $_POST);
            // //Controllo che il campo sia stato compilato
            if (empty($datiLavori["nome"])) {
                $nomeErr = "*Campo obbligatorio";
            } else {
                $nome = $datiLavori["nome"];
                // controllo se il nome contiene solo lettere, numeri e spazi vuoti
                if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $nome)) {
                    $nomeErr = "Sono ammessi solo lettere, numeri e spazi";
                    $nome = "";
                    //Controllo che la lunghezza sia compresa tra 3 e 30 caratteri
                } else if (strlen($nome) < 3 || strlen($nome) > 30) {
                    $nomeErr = "La lunghezza deve essere compresa tra 3 e 30 caratteri";
                    $nome = "";
                } else {
                    $success++;
                }
            }

            //Se la variabile success è uguale a 1 proseguo conla modifica dei dati
            if ($success == 1) {
                try {
                    $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
                    $query = "UPDATE lavori
                                    SET nome= :nome,  
                                    descrizione= :descrizione
                                    WHERE id= :id";
                    $check = $pdo->prepare($query);
                    $check->bindParam(":nome", $nome, PDO::PARAM_STR);
                    $check->bindParam(":descrizione", $datiLavori["descrizione"], PDO::PARAM_STR);
                    $check->bindParam(":id", $idSel, PDO::PARAM_INT);
                    $check->execute();
                    if ($check->rowCount() >= 0) {
                        echo '<script type="text/javascript">',
                        'window.alert("Dati modificati con successo");',
                        'location.href="./lavori.php?op=VIS&idSel=";' .
                            '</script>';
                    }
                } catch (PDOException $e) {
                    echo "<br><br>Errore PDO: " . $e->getMessage();
                }
            }
        }


        // Se viene premuto il pulsante per il caricamento dell' immagine
        if (isset($_POST['upload'])) {
            $uploadOk = 1;
            $fileName = $_FILES["uploadFile"]["name"];
            $tempName = $_FILES["uploadFile"]["tmp_name"];
            $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));


            // Controllo che la dimensione sia corretta 
            if ($_FILES["uploadFile"]["size"] > 5000000) {
                $msg = "Il file deve essere inferiore a 5Mb.";
                $uploadOk = 0;
            }

            // Controllo che l' immagine sia in un formato valido
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $msg = "Sono ammessi solo i formati: JPG, JPEG, PNG & GIF.";
                $uploadOk = 0;
            }

            // Controllo che non ci siano stati errori e provo a caricare il file
            if ($uploadOk == 1) {
                $id = $datiLavori["id"];
                // Verifico se esiste la cartella dove inseire l' immagine
                if (!file_exists('./uploads/' . $id)) {
                    //Se non esiste la creo
                    mkdir('uploads/' . $id . '/', 0777, true);
                }
                //Percorso di destinazine dell' immagine
                $folder = './uploads/' . $id . '/' . $fileName;
                $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
                $query = "UPDATE lavori
                    SET immagine = :immagine
                    WHERE id= :id";
                $check = $pdo->prepare($query);
                $check->bindParam(":immagine", $fileName, PDO::PARAM_STR);
                $check->bindParam(":id", $idSel, PDO::PARAM_INT);

                // Provo a inserire l' immagine nella cartella
                if (move_uploaded_file($tempName, $folder)) {
                    $check->execute();
                    $msg .= 'Immagine caricata con successo';
                    echo "<meta http-equiv='refresh' content='0'>";
                } else {
                    $msg .= 'Errore caricamento';
                }
            }
        }
        //Se viene premuto il pulsante per eliminare l' immagine
        if (isset($_POST['elimina'])) {
           
            try {
                $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
                $query = "UPDATE lavori
                SET immagine=NULL  
                WHERE id= :id";
                $check = $pdo->prepare($query);
                $check->bindParam(":id", $idSel, PDO::PARAM_INT);
                $check->execute();

                if ($check->rowCount() > 0) {
                    var_dump($check->rowCount());
                    //elimino la directory contenente l' immagine
                    $directory = 'uploads/' . $idSel;
                    delete_directory($directory);
                }
            } catch (PDOException $e) {
                echo "<br><br>Errore PDO: " . $e->getMessage();
            }
        }
        //creo la pagina
        echo '<body>';
        echo '<div class="paginaModificaLavori">';
        echo '<div class="return" onclick=fnReturn()>< Indietro</div>';
        echo '<div class="containerTitolo">';
        echo '<h1>Modifica</h1>';
        echo '</div>';
        echo '<div class="contenuto">';
        echo creaFormModificaLavori($datiLavori, $nomeErr, $msg);
        echo '</div>';
        echo '</div>';
    }


    ////////////////CANCELLA LAVORI/////////////////////////////////////////////////////////
    else if ($op == 'CANC') {
        try {
            $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
            $query = "DELETE FROM lavori 
                  WHERE id = :id;";
            $check = $pdo->prepare($query);
            $check->bindParam("id", $idSel, PDO::PARAM_INT);
            $check->execute();

            if ($check->rowCount() > 0) {
                //elimino la directory contenente l' immagine
                $directory = 'uploads/' . $idSel;
                delete_directory($directory);
                echo '<script type="text/javascript">',
                'window.alert("Elemento cancellato");',
                'location.href="./lavori.php?op=VIS&idSel=";' .
                    '</script>';
            }
        } catch (PDOException $e) {
            echo "<br><br>Errore PDO: " . $e->getMessage();
        }
    }
    /////////////// PAGINA AGGIUNGI LAVORI//////////////////////////////////////////////
    else if ($op == 'ADD') {

        //Definisco le variabli e setto il valore su vuoto
        $nomeErr = $success = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {


            //Controllo che il campo sia stato compilato
            if (empty($_POST["nome"])) {
                $nomeErr = "*Campo obbligatorio";
            } else {
                // controllo che il nome contenga solo lettere, numeri e spazi vuoti
                if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $_POST["nome"])) {
                    $nomeErr = "Sono ammessi solo lettere, numeri e spazi";
                    $_POST["nome"] = "";
                } else if (strlen($_POST["nome"]) < 3 || strlen($_POST["nome"]) > 30) {
                    $nomeErr = "La lunghezza deve essere compresa tra 3 e 30 caratteri";
                    $_POST["nome"] = "";
                } else {
                    $nome = $_POST["nome"];
                    $success++;
                }
            }

            // Se non ci sono errori proseguo con la creazione dell' elemento
            if ($success == 1) {
                $nome = $_POST["nome"];
                $descrizione = $_POST["descrizione"];
                try {
                    $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
                    $query = "
                 INSERT INTO lavori
                 VALUES (0, :nome, :descrizione, 0)";
                    $check = $pdo->prepare($query);
                    $check->bindParam(":nome", $nome, PDO::PARAM_STR);
                    $check->bindParam(":descrizione", $descrizione, PDO::PARAM_STR);
                    $check->execute();
                    //Se la query viene eseguita faccio apparire il messaggio corrispondente
                    if ($check->rowCount() > 0) {
                        echo '<script type="text/javascript">',
                        'window.alert("Elemento aggiunto correttamente");',
                        'location.href="./lavori.php?op=VIS&idSel=";' .
                            '</script>';
                    } else {
                        echo '<script type="text/javascript">',
                        'window.alert("problema con l\' inserimento dei dati");',
                        '</script>';
                    }
                } catch (PDOException $e) {
                    echo "<br><br>Errore PDO: " . $e->getMessage();
                }
            }
        }
        //creo la pagina
        echo ('<div class="paginaAggiungiLavori">');
        echo '<div class="return" onclick=fnReturn()>< Indietro</div>';
        echo ('<div class="containerTitolo">');
        echo ('<h1>Aggiungi Lavori</h1>');
        echo ('</div>');
        echo creaFormAggiungiLavori($nomeErr);
        echo ('</div>');
    }
    echo creaFinePagina();
    echo '</body>';
} else {
    //Se non è stato effettuato il login faccio apparire il messaggio di errore
    echo creaInizioPagina();
    echo creaMenuNavbar();
    echo ('<div class="noLog">Effettua il  <a href="./login.php?op=LOG">login</a> per accdere all\'area riservata.</div>');
    echo creaFinePagina();
}
