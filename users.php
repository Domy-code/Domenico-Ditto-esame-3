<?php
session_start();
//verifico l' esistenza di una sessione gia' avviata
if (isset($_SESSION['session_id'])) {
    //Acquisisco le informazioni sul tipo di utente
    $session_privilegi = htmlspecialchars($_SESSION['session_privilegi'], ENT_QUOTES, 'UTF-8');
    require_once("funzioni.php");
    require_once("dati.php");
    //creo la pagina
    echo creaInizioPagina();
    echo creaMenuNavbar();
    $dati = [];
    $datiUtente = [];
    $menu = '';
    //Acquisiso i parametri della query string
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
                       cognome,
                       username,
                       email,
                       password,
                       privilegi,
                        CASE 
                        WHEN privilegi = 0 THEN 'NO' 
                        WHEN privilegi = 1 THEN 'SI'
                        ELSE ''
                        END AS privilegi,
                       sesso,
                        CASE 
                        WHEN sesso = 0 THEN 'Maschio'
                        WHEN sesso = 1 THEN 'Femmina'
                        WHEN sesso = 2 THEN 'Altro' 
                        ELSE ''
                        END AS sesso,
                       dataNascita FROM db39opf4ki7vt9.utenti
                        ORDER BY utenti.cognome,utenti.nome;";
        $query = $pdo->prepare($sql);
        $query->execute();
        if ($query->rowCount() > 0) {
            while ($righe = $query->fetch(PDO::FETCH_ASSOC)) {
                $tmp = array(
                    "id" => $righe["id"],
                    "nome" => $righe["nome"],
                    "cognome" => $righe["cognome"],
                    "username" => $righe["username"],
                    "sesso" => $righe["sesso"],
                    "password" => $righe["password"],
                    "dataNascita" => $righe["dataNascita"],
                    "email" => $righe["email"],
                    "privilegi" => $righe["privilegi"],
                );
                $dati[] = $tmp;
                if ($idSel != null && $idSel == $tmp["id"]) {
                    $datiUtente = $tmp;
                }
            }
            //se non è stato selezionato alcun utente viene selezionato il primo della lista
            if ($idSel == null) {
                $idSel = $dati[0]["id"];
                $datiUtente = $dati[0];
            }
        }
    } catch (PDOException $e) {
        echo "<br><br>Errore PDO: " . $e->getMessage();
    }
    //**************************** CREO HTML ***********************************

    ///////////////// PAGINA VISUALIZZAZIONE UTENTI //////////////////////////////
    if ($op == "VIS") {
        //creo la pagina
        echo '<body>';
        echo '<div class="paginaVisualizzazioneUtenti">';
        echo '<div class="containerTitolo">';
        echo '<a href="/dashboard.php?op=MEN" class="return" title="Torna indietro" style="text-decoration:none;">< Indietro</a>';
        echo '<h1>Lista utenti</h1>';
        echo '</div>';
        echo '<div class="flex-container">';
        echo '<div class="listaUtenti">';
        echo creaElencoUtenti($dati, $idSel);
        echo '</div>';
        echo '<div class="contenuto">';
        echo creaTitolo($datiUtente);
        echo creaFormVisualizzazioneUtenti($datiUtente);
        echo '</div>';
        //Se l' utente ha i privilegi permetto la visualizzazione dei pulsanti di modifica
        if ($session_privilegi == 1) {
            echo creaPulsantiOperazioni();
        }
        echo '</div>';
        echo '</div>';
        echo creaFinePagina();
        echo '</body>';
    }
    /////////////// PAGINA MODIFICA //////////////////////////////////////////////
    else if ($op == 'MOD') {

        $nomeErr = $cognomeErr  = $sessoErr = $dataNascitaErr = $emailErr = $passwordErr = $ripPasswordErr = $success = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $datiUtente = array_replace($datiUtente, $_POST);
///////////////////////////FORM VALIDATION LATO SERVER//////////////////////////////////////////////////////////
            // //Controllo che il campo sia stato compilato
            if (empty($_POST["nome"])) {
                $nomeErr = "*Campo obbligatorio";
            } else {

                $nome = $_POST["nome"];
                // controllo se il nome contiene solo lettere e spazi vuoti
                if (!preg_match("/^[a-zA-Z-' ]*$/", $nome)) {
                    $nomeErr = "Sono ammesse solo lettere e spazi";
                    $nome = "";
                } else if (strlen($nome) < 3 || strlen($nome) > 20) {
                    $nomeErr = "La lunghezza deve essere compresa tra 3 e 20 caratteri";
                    $nome = "";
                } else {
                    $success++;
                }
            }
            //Controllo che il campo sia stato compilato
            if (empty($_POST["cognome"])) {
                $cognomeErr = "*Campo obbligatorio";
            } else {
                $cognome = $_POST["cognome"];
                // controllo se il cognome contiene solo lettere e spazi vuoti
                if (!preg_match("/^[a-zA-Z-' ]*$/", $cognome)) {
                    $cognomeErr = "Sono ammesse solo lettere e spazi";
                    $cognome = "";
                } elseif (strlen($cognome) < 3 || strlen($cognome) > 30) {
                    $cognomeErr = "La lunghezza deve essere compresa tra 3 e 30 caratteri";
                    $cognome = "";
                } else {
                    $success++;
                }
            }

            //Controllo che il campo sia stato selezionato
            if ($_POST["sesso"] == '') {
                $sessoErr = "Seleziona un elemento";
            } else {
                $sesso = $_POST["sesso"];
                if ($sesso == "Maschio") {
                    $sesso = 0;
                } else if ($sesso == "Femmina") {
                    $sesso = 1;
                } else if ($sesso == "Altro") {
                    $sesso = 2;
                }
                $success++;
            }
            //Controllo che il campo sia stato compilato
            if (empty($_POST["dataNascita"])) {
                $dataNascitaErr = "*Campo obbligatorio";
            } else {
                $dataNascita = $_POST["dataNascita"];
                $success++;
            }
            //Controllo che il campo sia stato compilato
            if (empty($_POST["email"])) {
                $emailErr = "*Campo obbligatorio";
            } else {
                $email = $_POST["email"];
                // Controllo se la mail sia stata scritta in un formato valido
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Formato email non valido";
                    $email = "";
                } else {
                    $success++;
                }
            }
            //Controllo che il campo sia stato compilato
            if (empty($_POST["password"])) {
                $passwordErr = "*Campo obbligatorio";
            } else {
                $password = $_POST["password"];
                // Controllo se la password sia scritta in un formato valido
                if (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/", $password)) {
                    $passwordErr = "La password deve iniziare con un numero o una lettera maiuscola";
                    $password = "";
                } else if (strlen(($password) < 8 || strlen($password)) > 20) {
                    $passwordErr = "La lunghezza deve essere compresa tra 8 e 20 caratteri";
                }
            }
            //Controllo che il campo sia stato compilato
            if (empty($_POST["ripPassword"])) {
                $ripPasswordErr = "*Campo obbligatorio";
            } else {
                $ripPassword = $_POST["ripPassword"];
                // Controllo che le password coincidano
                if ($ripPassword != $password) {
                    $ripPasswordErr = "Le password non coincidono";
                    $ripPassword = "";
                    $password = "";
                } else {
                    $success++;
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                }
            }
         //se non ci sono stai errori proseguo con la modifica 
            if ($success == 6) {
                $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
                $query = "UPDATE utenti
                                SET nome= :nome, 
                                cognome= :cognome,  
                                sesso= :sesso, 
                                dataNascita= :dataNascita, 
                                email= :email, 
                                password= :password
                                WHERE id = :id";
                $check = $pdo->prepare($query);
                $check->bindParam(":nome", $nome, PDO::PARAM_STR);
                $check->bindParam(":cognome", $cognome, PDO::PARAM_STR);
                $check->bindParam(":sesso", $sesso, PDO::PARAM_INT);
                $check->bindParam(":dataNascita", $dataNascita, PDO::PARAM_STR);
                $check->bindParam(":email", $email, PDO::PARAM_STR);
                $check->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $check->bindParam("id", $idSel, PDO::PARAM_INT);
                $check->execute();
                //se la query è stata eseguita correttamente faccio apparire il messaggio di conferma
                if ($check->rowCount() > 0) {
                    echo '<script type="text/javascript">',
                    'window.alert("Dati modificati correttamente");',
                    'location.href="./users.php?op=VIS";' .
                        '</script>';
                    $nome = $cognome = $username = $sesso = $dataNascita = $email = $password = $ripPassword = $password_hash = '';
                } else {
                    echo ("Problema con l' inserimento dei dati");
                }
            }
        }
?>
        <script>
        //////////////////////FORM VALIDATION LATO CLIENT/////////////////////////////////////////////////////////
        //controllo lato client se i campi sono stati compilati correttamente
        function submitForm() {
            const form = document.getElementById('formModificaUtente');
            let nome = document.getElementById("nome");
            let errNome = document.getElementById("nomeErr");
            let regexNome = /^[a-zA-Z-' ]*$/;
            let cognome = document.getElementById("cognome");
            let errCognome = document.getElementById("cognomeErr");
            let sesso = document.getElementById("sesso");
            let errSesso = document.getElementById("sessoErr");
            let dataNascita = document.getElementById("dataNascita");
            let inputDate = new Date(dataNascita.value);
            let dataOdierna = new Date();
            let errData = document.getElementById("dataErr");
            let email = document.getElementById("email");
            let errEmail = document.getElementById("emailErr");
            let regexEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            let password = document.getElementById("password");
            let errPassword = document.getElementById("passwordErr");
            let regexPassword = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/;
            let ripPassword = document.getElementById("ripPassword");
            let errRipPassword = document.getElementById("ripPasswordErr")
            let success = 0;



            //Verifico che il campo nome non sia vuoto
            if (nome.value == "") {
                nome.style.borderBottomColor = "#ff0000b0";
                errNome.innerHTML = "Campo obbligatorio";
            }
            // controllo se il nome contiene solo lettere e spazi vuoti
            else if (!regexNome.test(nome.value)) {
                nome.style.borderBottomColor = "#ff0000b0";
                errNome.innerHTML = "Sono ammesse solo lettere e spazi";
            }
            //controllo che la lunghezza del campo sia corretta
            else if (nome.value.length < 3 || nome.value.length > 20) {
                nome.style.borderBottomColor = "#ff0000b0";
                errNome.innerHTML = "La lunghezza deve essere compresa tra 3 e 20 caratteri";
            } else {
                success++;
                errNome.innerHTML = "";
                nome.style.borderBottomColor = "#afafaf";
            }

            //Verifico che il campo nome non sia vuoto
            if (cognome.value == "") {
                cognome.style.borderBottomColor = "#ff0000b0";
                errCognome.innerHTML = "Campo obbligatorio";
            }
            // controllo se il nome contiene solo lettere e spazi vuoti
            else if (!regexNome.test(cognome.value)) {
                cognome.style.borderBottomColor = "#ff0000b0";
                errCognome.innerHTML = "Sono ammesse solo lettere e spazi";
            }
            //controllo che la lunghezza del campo sia corretta
            else if (cognome.value.length < 3 || cognome.value.length > 30) {
                cognome.style.borderBottomColor = "#ff0000b0";
                errCognome.innerHTML = "La lunghezza deve essere compresa tra 3 e 30 caratteri";
            } else {
                success++;
                errCognome.innerHTML = "";
                cognome.style.borderBottomColor = "#afafaf";
            }

            //Verifico che il campo nome non sia vuoto
            if (sesso.value == "") {
                sesso.style.borderColor = "#ff0000b0";
                errSesso.innerHTML = "Campo obbligatorio";
            } else {
                success++;
                errSesso.innerHTML = "";
                sesso.style.borderColor = "#afafaf";
            }

            //Verifico che il campo nome non sia vuoto
            if (dataNascita.value == "") {
                dataNascita.style.borderColor = "#ff0000b0";
                document.getElementById("dataNascita").style.borderColor = "#ff0000b0";
                errData.innerHTML = "Campo obbligatorio";
            } else if (inputDate > dataOdierna) {
                //verifico che la data non sia nel futuro
                errData.innerHTML = "Inserire una data corretta";
                document.getElementById("dataNascita").style.borderColor = "#ff0000b0";
            } else {
                success++;
                document.getElementById("dataNascita").style.borderColor = "#afafaf";
                dataNascita.style.borderColor = "#afafaf";
                errData.innerHTML = "";
            }

            //Verifico che il campo non sia vuoto
            if (email.value == "") {
                email.style.borderBottomColor = "#ff0000b0";
                errEmail.innerHTML = "Campo obbligatorio";
            }
            // controllo se la mail è scritta in un formato corretto
            else if (!regexEmail.test(email.value)) {
                email.style.borderBottomColor = "#ff0000b0";
                errEmail.innerHTML = "Email non valida";
            } else {
                success++;
                errEmail.innerHTML = "";
                email.style.borderBottomColor = "#afafaf";
            }


            //Verifico che il campo non sia vuoto
            if (password.value == "") {
                password.style.borderBottomColor = "#ff0000b0";
                errPassword.innerHTML = "Campo obbligatorio*";
            }
            // controllo se il campo è in un formato corretto
            else if (!regexPassword.test(password.value)) {
        
                password.style.borderBottomColor = "#ff0000b0";
                errPassword.innerHTML = "La password deve iniziare con una lettera maiuscola <br>Deve contenere almeno un numero e una lettera minuscola";

            } else if (password.value.lenght < 8 || password.value.lenght > 20) {
                password.style.borderBottomColor = "#ff0000b0";
                errPassword.innerHTML = "La lunghezza deve essere tra 8 e 20 caratteri";
            } else {
                success++;
                errPassword.innerHTML = "";
                password.style.borderBottomColor = "#afafaf";
            }

            //verifico che il campo sia stato compilato
            if (ripPassword.value == ""){
                ripPassword.style.borderBottomColor = "#ff0000b0";
                errRipPassword.innerHTML = "Campo obbligatorio*";
            }//verifico che le password coincidano
            else if (ripPassword.value != password.value) {
                ripPassword.style.borderBottomColor = "#ff0000b0";
                errRipPassword.innerHTML = "Le password non coincidono";
            } else {
                success++;
                errRipPassword.innerHTML="";
                ripPassword.style.bordeBottomrColor="#2DCA07";
                password.style.borderBottomColor="#2DCA07";
            }

            // Se non ci sono errori proseguo col submit del form
            if (success == 7) {
                form.submit();
                success = 0;
            }
        }
    </script>
    <?php
        //creo la pagina
        echo '<body>';
        echo '<div class="paginaModificaUtente">';
        echo '<div class="return" onclick=fnReturn()>< Indietro</div>';
        echo '<div class="contenuto">';
        echo '<div class="contenitoreTitolo">';
        echo '<h1>Modifica</h1>';
        echo '</div>';
        echo creaFormModificaUtente($datiUtente, $nomeErr, $cognomeErr, $sessoErr, $dataNascitaErr, $emailErr, $passwordErr, $ripPasswordErr);
        echo '</div>';
        echo '</div>';
        echo creaFinePagina();
        echo '</body>';
    }
    ///////////////CANCELLA////////////////////////////////////////////////
    else if ($op == 'CANC') {
        $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
        $query = "DELETE FROM utenti 
                      WHERE id = :id;";
        $check = $pdo->prepare($query);
        $check->bindParam("id", $idSel, PDO::PARAM_INT);
        $check->execute();
            if ($check->rowCount() > 0) {
                echo '<script type="text/javascript">',
                'window.alert("Utente cancellato");',
                'location.href="./users.php?op=VIS&idSel=";' ,
                '</script>';
            }
    }
    ///////////////AGGIUNGI//////////////////////////////////
     else if ($op == 'ADD') {
        echo '<script>
        window.onload = function() {
         window.location.replace("/login.php?op=REG");
     } </script>';
    } 
}else {
    //Se non è stato effettuato il login faccio apparire il messaggio di errore
    echo creaInizioPagina();
    echo creaMenuNavbar();
    echo('<div class="noLog">Effettua il  <a href="./login.php?op=LOG">login</a> per accdere all\'area riservata.</div>');
    echo creaFinePagina();
}

