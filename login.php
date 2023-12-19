<?php
session_start();
require_once("funzioni.php");
require_once("dati.php");
echo creaInizioPagina();
echo creaMenuNavbar();
//Acquisisco i parametri della query string
if ($_GET["op"] == "LOG") {
    // se la sessione è gia esistente reindirizzo l' utente alla dashboard
    if (isset($_SESSION['session_id'])) {
        echo '<script>
       window.onload = function() {
        window.location.replace("/dashboard.php?op=MEN");
    } </script>';
        //Altrimenti creo la pagina di login
    } else {
        echo creaLogin();
    }
    //Se viene premuto il pulsante di login richiamo la funzione per avviare la sessione dell' utente
    if (isset($_POST['login'])) {
        funcLogin();
    }
}
//////////////////////PAGINA REGISTRAZIONE///////////////////////////////////////////////////////////
if ($_GET["op"] == "REG") {
    // Definisco le variabli e setto il valore su vuoto
    $nomeErr = $cognomeErr = $usernameErr = $sessoErr = $dataNascitaErr = $emailErr = $passwordErr = $ripPasswordErr = "";
    $nome = $cognome = $username = $sesso = $sessoInt = $dataNascita = $email = $password = $ripPassword = $checkUsername = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // //Controllo che il campo sia stato compilato
        if (empty($_POST["nome"])) {
            $nomeErr = "*Campo obbligatorio";
        } else {
            $nome = $_POST["nome"];
            // controllo che il nome contenga solo lettere e spazi vuoti
            if (!preg_match("/^[a-zA-Z-' ]*$/", $nome)) {
                $nomeErr = "Sono ammessi solo lettere e spazi";
                $nome = "";
            } else if (strlen($nome) < 3 || strlen($nome) > 20) {
                $nomeErr = "La lunghezza deve essere compresa tra 3 e 20 caratteri";
                $nome = "";
            } else {
                $success++;
            }
        }
        //Controllo se il campo sia stato compilato
        if (empty($_POST["cognome"])) {
            $cognomeErr = "*Campo obbligatorio";
        } else {
            $cognome = $_POST["cognome"];
            // controllo che il cognome contenga solo lettere e spazi vuoti
            if (!preg_match("/^[a-zA-Z-' ]*$/", $cognome)) {
                $cognomeErr = "Sono ammessi solo lettere e spazi";
                $cognome = "";
            } elseif (strlen($cognome) < 3 || strlen($cognome) > 30) {
                $cognomeErr = "La lunghezza deve essere compresa tra 3 e 30 caratteri";
                $cognome = "";
            } else {
                $success++;
            }
        }

        //Controllo se il campo sia stato compilato
        if (empty($_POST["username"])) {
            $usernameErr = "*Campo obbligatorio";
        } else {
            $username = $_POST["username"];
            // controllo se l' username contiene solo lettere e spazi vuoti
            if (!preg_match("/[a-zA-Z0-9]/", $username)) {
                $usernameErr = "Sono ammessi solo lettere e numeri";
                $username = "";
            } elseif (strlen($username) < 5 || strlen($nome) > 20) {
                $usernameErr = "La lunghezza deve essere compresa tra 5 e 20 caratteri";
                $username = "";
            } else {
                $success++;
            }
        }

        //Controllo che il campo sia stato selezionato
        if ($_POST["sesso"] == '') {
            $sessoErr = "Seleziona un elemento";
        } else {
            $sesso = $_POST["sesso"];
            $success++;
            $clsSelVuoto = ($sesso == "") ? ' selected ' : '';
            $clsSelMaschio = ($sesso == "Maschio") ? ' selected ' : '';
            $clsSelFemmina = ($sesso == "Femmina") ? ' selected ' : '';
            $clsSelAltro = ($sesso == 'Altro') ? ' selected ' : '';
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

        //Controllo se il campo sia stato compilato
        if (empty($_POST["password"])) {
            $passwordErr = "*Campo obbligatorio";
        } else {
            $password = $_POST["password"];
            // Controllo se la password sia scritta in un formato valido
            if (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/", $password)) {
                $passwordErr = "La password deve iniziare con una lettera maiuscol e deve contenere almeno un numero";
                $password = "";
            } else if (strlen(($password) < 8 || strlen($password)) > 20) {
                $passwordErr = "La lunghezza deve essere compresa tra 8 e 20 caratteri";
            }
        }

        //Controllo se il campo sia stato compilato
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
    }
    // se non ci sono errori procedo con la registrazione dell' utente
    if ($success == 7) {
        $query = "
            SELECT id
            FROM utenti
            WHERE username = :username;";
        try {
            $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
            $checkUsername = $pdo->prepare($query);
            $checkUsername->bindParam(':username', $username, PDO::PARAM_STR);
            $checkUsername->execute();
            $user = $checkUsername->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<br><br>Errore PDO: " . $e->getMessage();
        }

        if (count($user) > 0) {
            $usernameErr = 'Username già in uso';
        } else {

            $privilegi = 0;
            $query = "
            INSERT INTO utenti
            VALUES (0, :username, :nome, :cognome, :sesso, :dataNascita, :email, :password, :privilegi)
            ";
            try {
                $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
                $check = $pdo->prepare($query);
                $check->bindParam(":nome", $nome, PDO::PARAM_STR);
                $check->bindParam(":cognome", $cognome, PDO::PARAM_STR);
                $check->bindParam(':username', $username, PDO::PARAM_STR);
                $check->bindParam(":sesso", $sesso, PDO::PARAM_INT);
                $check->bindParam(":dataNascita", $dataNascita, PDO::PARAM_STR);
                $check->bindParam(":email", $email, PDO::PARAM_STR);
                $check->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $check->bindParam(':privilegi', $privilegi, PDO::PARAM_INT);
                $check->execute();
                // se la query viene eseguita correttamente faccio apparire il messaggio di operazione avvenuta con successo
                if ($check->rowCount() > 0) {
                    $nome = $cognome = $username = $sesso = $dataNascita = $email = $password = $ripPassword = $password_hash = "";
                    echo '<script>
                    window.onload = function() {
                     window.location.replace("/login.php?op=REGSUCC");
                 } </script>';
                    //altrimenti il messaggio di errore
                } else {
                    echo ('Problemi con l\'inserimento dei dati %s');
                }
            } catch (PDOException $e) {
                echo "<br><br>Errore PDO: " . $e->getMessage();
            }
        }
    }

?>
    <div class="paginaRegistrazione">
        <div class="registrazione">
            <a href="./login.php?op=LOG" class="return">
                < indietro </a>
                    <h1>Registrazione</h1>

                    <form method="post" action="" id="formRegistrazione">

                        <input type="text" id="nome" placeholder="Nome*" name="nome" value="<?php echo $nome; ?>">
                        <span class="error" id="nomeErr">
                            <?php echo $nomeErr; ?>
                        </span>

                        <input type="text" id="cognome" placeholder="Cognome*" name="cognome" value="<?php echo $cognome; ?>">
                        <span class="error" id="cognomeErr">
                            <?php echo $cognomeErr; ?>
                        </span>

                        <input type="text" id="username" placeholder="Username*" name="username" value="<?php echo $username; ?>">
                        <span class="error" id="usernameErr">
                            <?php echo $usernameErr; ?>
                        </span>
                        <div class="selectSessoContainer">
                            <div class="custom-select">
                                <select class="sesso" name="sesso" id="sesso">
                                    <option value="" <?php $clsSelVuoto ?>>Genere</option>
                                    <option value="0" <?php $clsSelMaschio ?>>Maschio</option>
                                    <option value="1" <?php $clsSelFemmina ?>>Femmina</option>
                                    <option value="2" <?php $clsSelAltro ?>>Altro</option>
                                </select>
                            </div>
                        </div>
                        <span class="error" id="sessoErr">
                            <?php echo $sessoErr; ?>
                        </span>
                        <div class="dateContainer" id="dateContainer">
                            <label for="dataNascita">Data di nascita</label>
                            <input type="date" id="dataNascita" placeholder="Data di Nascita*" name="dataNascita" value="<?php echo $dataNascita; ?>">
                        </div>
                        <span class="error" id="dataErr">
                            <?php echo $dataNascitaErr; ?>
                        </span>

                        <input type="text" id="email" name="email" placeholder="E-mail*" value="<?php echo $email; ?>">
                        <span class="error" id="emailErr">
                            <?php echo $emailErr; ?>
                        </span>

                        <input type="password" id="password" placeholder="Password*" name="password" value="<?php echo $password; ?>">
                        <span class="error" id="passwordErr">
                            <?php echo $passwordErr; ?>
                        </span>

                        <input type="password" id="ripPassword" placeholder="Ripeti Password*" name="ripPassword" value="<?php echo $ripPassword; ?>" onkeyup="matchPassword()">
                        <span class="error" id="ripPasswordErr">
                            <?php echo $ripPasswordErr; ?>
                        </span>
                        <div class="containerPrivacy">
                            <label for="privacyCheck" class=textPrivacy>
                                Ho preso visione della <a href="https://www.iubenda.com/privacy-policy/80605050.pdf">privacy policy</a> e acconsento al trattamento dei dati personali.
                            </label>
                            <input type="checkbox" id="privacyCheck" name="privacyCheck">
                        </div>
                        <span class="error" id="privacyError"></span>
                        <div class="btnContainer">
                            <div class="overButton" id="overButton" onclick="privacyMsgError()"></div>
                            <button type="button" id="btnReg" name="registra" onclick="submitForm()" disabled>Registrati</button>
                        </div>
                        <script type="application/javascript">
                            var checkbox = document.querySelector("input[name=privacyCheck]");
                            checkbox.addEventListener('change', function() {
                                if (this.checked) {
                                    document.getElementById("btnReg").disabled = false;
                                    document.getElementById("btnReg").style.cursor = 'pointer';
                                    document.getElementById("overButton").style.zIndex = "0";
                                    document.getElementById("privacyError").innerHTML = "";
                                } else {
                                    document.getElementById("btnReg").disabled = true;
                                    document.getElementById("btnReg").style.cursor = 'not-allowed';
                                    document.getElementById("overButton").style.zIndex = "1";
                                }

                            });
                        </script>

                    </form>
                    <script>
                        //////////////////////FORM VALIDATION LATO CLIENT/////////////////////////////////////////////////////////
                        //controllo lato client se i campi sono stati compilati correttamente
                        function submitForm() {
                            const form = document.getElementById('formRegistrazione');
                            let nome = document.getElementById("nome");
                            let errNome = document.getElementById("nomeErr");
                            let regexNome = /^[a-zA-Z-' ]*$/;
                            let cognome = document.getElementById("cognome");
                            let errCognome = document.getElementById("cognomeErr");
                            let username = document.getElementById("username");
                            let errUsername = document.getElementById("usernameErr");
                            let regexUsername = /[a-zA-Z0-9]/;
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
                                nome.style.borderColor = "#ff0000b0";
                                errNome.innerHTML = "Campo obbligatorio";
                            }
                            // controllo se il nome contiene solo lettere e spazi vuoti
                            else if (!regexNome.test(nome.value)) {
                                nome.style.borderColor = "#ff0000b0";
                                errNome.innerHTML = "Sono ammesse solo lettere e spazi";
                            }
                            //controllo che la lunghezza del campo sia corretta
                            else if (nome.value.length < 3 || nome.value.length > 20) {
                                nome.style.borderColor = "#ff0000b0";
                                errNome.innerHTML = "La lunghezza deve essere compresa tra 3 e 20 caratteri";
                            } else {
                                success++;
                                errNome.innerHTML = "";
                                nome.style.borderColor = "#afafaf";
                            }

                            //Verifico che il campo nome non sia vuoto
                            if (cognome.value == "") {
                                cognome.style.borderColor = "#ff0000b0";
                                errCognome.innerHTML = "Campo obbligatorio";
                            }
                            // controllo se il nome contiene solo lettere e spazi vuoti
                            else if (!regexNome.test(cognome.value)) {
                                cognome.style.borderColor = "#ff0000b0";
                                errCognome.innerHTML = "Sono ammesse solo lettere e spazi";
                            }
                            //controllo che la lunghezza del campo sia corretta
                            else if (cognome.value.length < 3 || cognome.value.length > 30) {
                                cognome.style.borderColor = "#ff0000b0";
                                errCognome.innerHTML = "La lunghezza deve essere compresa tra 3 e 30 caratteri";
                            } else {
                                success++;
                                errCognome.innerHTML = "";
                                cognome.style.borderColor = "#afafaf";
                            }

                            //Verifico che il campo nome non sia vuoto
                            if (username.value == "") {
                                username.style.borderColor = "#ff0000b0";
                                errUsername.innerHTML = "Campo obbligatorio";
                            }
                            // controllo se il nome contiene solo lettere e spazi vuoti
                            else if (!regexUsername.test(username.value)) {
                                username.style.borderColor = "#ff0000b0";
                                errUsername = "Sono ammesse solo lettere e numeri";
                            }
                            //controllo che la lunghezza del campo sia corretta
                            else if (username.value.length < 5 || username.value.lenght > 20) {
                                username.style.borderColor = "#ff0000b0";
                                errUsername.innerHTML = "La lunghezza deve essere compresa tra 5 e 20 caratteri";
                            } else {
                                success++;
                                errUsername.innerHTML = "";
                                username.style.borderColor = "#afafaf";
                            }

                            //Verifico che il campo nome non sia vuoto
                            if (sesso.value == "") {
                                sesso.style.borderColor = "#ff0000b0";
                                errSesso.innerHTML = "Campo obbligatorio";
                            } else {
                                success++;
                                errSesso.innerHTML = "";
                                username.style.borderColor = "#afafaf";
                            }

                            //Verifico che il campo nome non sia vuoto
                            if (dataNascita.value == "") {
                                dataNascita.style.borderColor = "#ff0000b0";
                                document.getElementById("dateContainer").style.borderColor = "#ff0000b0";
                                errData.innerHTML = "Campo obbligatorio";
                            } else if (inputDate > dataOdierna) {
                                //verifico che la data non sia nel futuro
                                errData.innerHTML = "Inserire una data corretta";
                                document.getElementById("dateContainer").style.borderColor = "#ff0000b0";
                            } else {
                                success++;
                                document.getElementById("dateContainer").style.borderColor = "#afafaf";
                                dataNascita.style.borderColor = "#afafaf";
                                errData.innerHTML = "";
                            }

                            //Verifico che il campo non sia vuoto
                            if (email.value == "") {
                                email.style.borderColor = "#ff0000b0";
                                errEmail.innerHTML = "Campo obbligatorio";
                            }
                            // controllo se la mail è scritta in un formato corretto
                            else if (!regexEmail.test(email.value)) {
                                email.style.borderColor = "#ff0000b0";
                                errEmail.innerHTML = "Email non valida";
                            } else {
                                success++;
                                errEmail.innerHTML = "";
                                email.style.borderColor = "#afafaf";
                            }


                            //Verifico che il campo non sia vuoto
                            if (password.value == "") {
                                password.style.borderColor = "#ff0000b0";
                                errPassword.innerHTML = "Campo obbligatorio*";
                            }
                            // controllo se il campo è in un formato corretto
                            else if (!regexPassword.test(password.value)) {
                                password.style.borderColor = "#ff0000b0";
                                errPassword.innerHTML = "La password deve iniziare con una lettera maiuscola <br>Deve contenere almeno un numero e una lettera minuscola";

                            } else if (password.value.lenght < 8 || password.value.lenght > 20) {
                                password.style.borderColor = "#ff0000b0";
                                errPassword.innerHTML = "La lunghezza deve essere tra 8 e 20 caratteri";
                            } else {
                                success++;
                                errPassword.innerHTML = "";
                                password.style.borderColor = "#afafaf";
                            }

                            //verifico che il campo sia stato compilato
                            if (ripPassword.value == ""){
                                ripPassword.style.borderColor = "#ff0000b0";
                                errRipPassword.innerHTML = "Campo obbligatorio*";
                            }//verifico che le password coincidano
                            else if (ripPassword.value != password.value) {
                                ripPassword.style.borderColor = "#ff0000b0";
                                errRipPassword.innerHTML = "Le password non coincidono";
                            } else {
                                success++;
                                errRipPassword.innerHTML="";
                                ripPassword.style.borderColor="#2DCA07";
                                password.style.borderColor="#2DCA07";
                            }

                            // Se non ci sono errori proseguo col submit del form
                            if (success == 8) {
                                form.submit();
                                success = 0;
                            }
                        }
                    </script>
        </div>
    </div>

<?php
    echo ("</header>");
    //se la registrazione è avvenuta con successo faccio apparire il messaggio corrispondente
} else if ($_GET["op"] == "REGSUCC") {
    echo '<script type="text/javascript">',
    'window.alert("Utente registrato correttamente");',
    'location.href="./login.php?op=LOG";' .
        '</script>';
}
echo creaFinePagina();
