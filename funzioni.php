<?php

function creaInizioPagina()
{
    $str = '<!DOCTYPE html>
    <html lang="it">
    <head>
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="0c656fce-9619-4316-a87c-c4fda2cdd9e2" data-blockingmode="auto" type="text/javascript"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="./MINCSS/style.min.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="./img/favicon.png">
        <script type = "text/javascript" src="funzioni.js"></script>
        <title>Domenico Ditto</title>
    </head>
    <body>
    <header class="header">

    <div>
        <a href="./index.php"><img src="./img/favicon.png" alt="logo" style="cursor: pointer;"></a>
    </div>

    <input id="menu-toggle" type="checkbox">

    <label class="menu-button-container" for="menu-toggle">
        <span class="menu-button"></span>
    </label>';
    return $str;
}
function creaMenuNavbar()
{
    require_once('dati.php');
    try {
        $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);

        $sql = "SELECT `id`, `nome`, `url`, `title` FROM `menuNavBar`;";
        $query = $pdo->prepare($sql);
        $query->execute();
        if ($query->rowCount() > 0) {
            while ($righe = $query->fetch(PDO::FETCH_ASSOC)) {
                $tmp = array(
                    "id" => $righe["id"],
                    "nome" => $righe["nome"],
                    "url" => $righe["url"],
                    "title" => $righe["title"]
                );
                $dati[] = $tmp;
            }

            $menu = "";
            $tmpLi = '<li><a href="%s" title="%s">%s</a></li>';
            $menu .= '<ul class="menu">';

            foreach ($dati as $item) {
                $menu .= sprintf($tmpLi, $item["url"], $item["title"], $item["nome"] . '</a></li>');
            }
            $menu .= '</ul>';
            $menu .= '</header>';



            return $menu;
        }
    } catch (PDOException $e) {
        exit("<br><br>Errore PDO: " . $e->getMessage());
    }
}
function creaLogin()
{
    $str = '<div class = "paginaLogin">
                <div class="login">
                    <h1>Login</h1>
                    <form method="post" action="" id="formLogin">
                    <div class="containerInput">
                        <div class="containerUsername">
                        <input type="text" id="usernameLogin" placeholder="Username" name="username">
                        <div id="errLogUsrnm"></div>
                        </div>
                        <div class="containerPassword">
                        <div class="containerPswTgle">
                        <input type="password" id="passwordLogin" placeholder="Password" name="password">
                        <img src="./img/hide.png" id="mostraPassword" title="Mostra password" onclick="fnShowPassword()">
                        </div>
                        <div id="errLogPsw"></div>
                        </div>
                        </div>
                        
                        <p>Non hai un account? <a href="./login.php?op=REG" title="Registrati">Registrati</a> </p>
                        <button type="submit" id="btnLogin" name="login">Accedi</button>
                        <div id="errLogin"></div>
                    </form>
                </div>
            </div>';
    return $str;
}
function creaFinePagina()
{
    $str = '<footer>
    <div class="contenitoreRigaSocial">

        <ul class="rigaSocial">
            <li><a href="http://www.facebook.com"><img src="./img/facebook.png" alt="Facebook" title="Seguimi su Facebook"></a></li>
            <li><a href="http://www.instagram.com"><img src="./img/instagram.png" alt="Instagram" title="Seguimi su Instagram"></a></li>
            <li><a href="http://www.linkedin.com"><img src="./img/linkedin.png" alt="linkedin" title="Seguimi su Linkedin"></a></li>
        </ul>

    </div><!--contenitoreRigaSocial-->

    <div class="contenitoreRigaPrivacy">

        <ul class="rigaPrivacy">
            <li><a href="https://www.iubenda.com/privacy-policy/80605050" class="iubenda-black iubenda-noiframe iubenda-embed iubenda-noiframe " title="Privacy Policy ">Privacy Policy</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src="https://cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script></li>
        </ul>

    </div><!--contenitoreRigaPrivacy-->

    <!-- <div class="rigaValidatore">
        <p>
            <a href="http://jigsaw.w3.org/css-validator/check/referer">
                <img src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valido!" />
            </a>
        </p>
    </div> -->

</footer>

</body>
</html>';
    return $str;
}
function funcLogin()
{

    if (isset($_SESSION['session_id'])) {
        echo '<script>
        window.onload = function() {
         window.location.replace("/dashboard.php?op=MEN");
     } </script>';
    }

    if (isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';


        if (empty($username)) {
            echo '<script type="text/javascript">' .
                'document.getElementById("usernameLogin").style.borderColor = "#ff0000b0";' .
                'document.getElementById("errLogUsrnm").innerHTML += "Inserisci Username";' .
                '</script>';
        } else if (empty($password)) {
            echo '<script type="text/javascript">' .
                'document.getElementById("passwordLogin").style.borderColor = "#ff0000b0";' .
                'document.getElementById("errLogPsw").innerHTML += "Inserisci Password";' .
                '</script>';
        } else {
            $query = "
            SELECT username, password, privilegi, nome
            FROM utenti
            WHERE username = :username
        ";
            try {
                $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
                $check = $pdo->prepare($query);
                $check->bindParam(':username', $username, PDO::PARAM_STR);
                $check->execute();

                $user = $check->fetch(PDO::FETCH_ASSOC);

                if (!$user || (password_verify($password, $user['password']) === false)) {
                    echo '<script type="text/javascript">' .
                        'document.getElementById("passwordLogin").style.borderColor = "#ff0000b0";' .
                        'document.getElementById("usernameLogin").style.borderColor = "#ff0000b0";' .
                        'document.getElementById("errLogin").innerHTML += "Credenziali utente errate";' .
                        '</script>';
                } else {
                    session_regenerate_id();
                    $_SESSION['session_id'] = session_id();
                    $_SESSION['session_user'] = $user['username'];
                    $_SESSION['session_privilegi'] = $user['privilegi'];
                    $_SESSION['session_nome'] = $user['nome'];
                    echo '<script>
                    window.onload = function() {
                     window.location.replace("/dashboard.php?op=MEN");
                 } </script>';
                }
            } catch (PDOException $e) {
                echo "<br><br>Errore PDO: " . $e->getMessage();
            }
        }
    }
}
function creaGalleryLavori()
{
    require_once('dati.php');

    $datiLavori = [];

    if (isset($_GET["idSel"])) {
        $idSel = htmlspecialchars($_GET['idSel']);
    }

    try {
        $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);

        $sql = "SELECT lavori.id, lavori.nome, lavori.immagine FROM db39opf4ki7vt9.lavori;";
        $query = $pdo->prepare($sql);
        $query->execute();
        if ($query->rowCount() > 0) {
            while ($righe = $query->fetch(PDO::FETCH_ASSOC)) {
                $tmp = array(
                    "id" => $righe["id"],
                    "nome" => $righe["nome"],
                    "immagine" => $righe["immagine"],
                );
                $dati[] = $tmp;
            }


            $lavoriLi = "";
            $tmpLi = '<li><a href="%s"><img src="%s" alt="%s" title="Clicca per ulteriori dettagli">%s</a></li>';

            foreach ($dati as $item) {
                $tmpUrl = "/worksDetail.php?idSel=" . $item["id"];
                if (file_exists('./uploads/' . $item["id"] . '/' . $item["immagine"])) {
                    $lavoriLi .= sprintf($tmpLi, $tmpUrl, './uploads/' . $item["id"] . '/' . $item["immagine"], $item["nome"], $item["nome"]);
                } else {
                    $lavoriLi .= sprintf($tmpLi, $tmpUrl, './img/default-image.png', $item["nome"], $item["nome"]);
                }
            }
            return $lavoriLi;
        }
    } catch (PDOException $e) {
        exit("<br><br>Errore PDO: " . $e->getMessage());
    }
}

function creaTitolo($datiUtente)
{
    $str = '<h3 class="titolo">' . $datiUtente['nome'] . " " . $datiUtente["cognome"] . '</h3>';
    return $str;
}
function creaElencoUtenti($dati, $idSel)
{
    $listaUtenti = "";
    $url = "./users.php?op=VIS";
    $tmpLi = '<li %s><a href="%s" title="Visualizza le informazioni di %s %s">%s %s</a></li>';

    if (count($dati) > 0) {
        $listaUtenti .= '<ul>';


        foreach ($dati as $item) {
            $tmpCls = ($idSel == $item["id"]) ? ' class="selezionato" ' : '';
            $tmpUrl = $url . "&idSel=" . $item["id"];
            $listaUtenti .= sprintf($tmpLi, $tmpCls, $tmpUrl, $item["nome"], $item["cognome"], $item["nome"], $item["cognome"]);
        }

        $listaUtenti .= '</ul>';
    }
    return $listaUtenti;
}

function creaFormVisualizzazioneUtenti($datiUtente)
{
    $dateObj = date_create($datiUtente["dataNascita"]);
    $dateFormat = (date_format($dateObj, "d/m/Y"));
    $str = '
    <div class="datiUtente"><span class="grass">Nome: </span>' . $datiUtente["nome"] . '</div>' .
        '<div class="datiUtente"><span class="grass">Cognome: </span>' . $datiUtente["cognome"] . '</div>' .
        '<div class="datiUtente"><span class="grass">Username: </span>' . $datiUtente["username"] . '</div> ' .
        '<div class="datiUtente"><span class="grass">Sesso: </span>' . $datiUtente["sesso"] . '</div> ' .
        '<div class="datiUtente"><span class="grass">Data di Nascita: </span> ' . $dateFormat . '</div> ' .
        '<div class="datiUtente"><span class="grass">Email: </span>' . $datiUtente["email"] . '</div> ' .
        '<div class="datiUtente"><span class="grass">Privilegi: </span>' . $datiUtente["privilegi"] . '</div> ';
    return $str;
}

function creaPulsantiOperazioni()
{
    $str = '<form action = "" method="post" id="formPulsanti">
    <button type="button" onclick="fnAggiungi()">Aggiungi</button>
    <button type="button" onclick="fnModifica()">Modifica</button>
    <button type="button" onclick="fnCancella()">Cancella</button></form>';
    return $str;
}

function creaFormModificaUtente($datiUtente, $nomeErr, $cognomeErr, $sessoErr, $dataNascitaErr, $emailErr, $passwordErr, $ripPasswordErr)
{
    $clsSelVuoto = ($datiUtente["sesso"] == "") ? ' selected ' : '';
    $clsSelMaschio = ($datiUtente["sesso"] == "Maschio") ? ' selected ' : '';
    $clsSelFemmina = ($datiUtente["sesso"] == "Femmina") ? ' selected ' : '';
    $clsSelAltro = ($datiUtente['sesso'] == 'Altro') ? ' selected ' : '';
    $str = '<form action="" method="post" id="formModificaUtente">' .
        '<div class="fieldContainer">' .
        '<label for="nome">Nome:</label>' .
        '<input type="text" id="nome" name="nome" value="' . $datiUtente["nome"] . '">' .
        '<span class="error" id="nomeErr">' . $nomeErr . '</span>' .
        '</div>' .
        '<div class="fieldContainer">' .
        '<label for="cognome">Cognome:</label>' .
        '<input type="text" id="cognome" name="cognome" value="' . $datiUtente["cognome"] . '">' .
        '<span class="error" id="cognomeErr">' . $cognomeErr . '</span>' .
        '</div>' .
        '<div class="fieldContainer">' .
        '<select id="sesso" name="sesso">' .
        '<option value=""' . $clsSelVuoto . ' >Seleziona sesso</option>' .
        '<option value="Maschio" ' . $clsSelMaschio . '>Maschio</option>' .
        '<option value="Femmina"' . $clsSelFemmina . ' >Femmina</option>' .
        '<option value="Altro" ' . $clsSelAltro . '>Altro</option>' .
        '</select>' .
        '<span class="error" id="sessoErr">' . $sessoErr . '</span>' .
        '</div>' .
        '<div class="fieldContainer">' .
        '<label for="dataNascita">Data di nascita</label>' .
        '<input type="date" id="dataNascita" name="dataNascita" value="' . $datiUtente["dataNascita"] . '">' .
        '<span class="error" id="dataErr">' . $dataNascitaErr . '</span>' .
        '</div>' .
        '<div class="fieldContainer">' .
        '<label for="email">Email:</label>' .
        '<input type="text" name="email"  id="email" value="' . $datiUtente["email"] . '">' .
        '<span class="error" id="emailErr">' . $emailErr . '</span>' .
        '</div>' .
        '<div class="fieldContainer">' .
        '<label for="password">Password:</label>' .
        '<input type="password" id="password" name="password" value ="' . $datiUtente["password"] . '">' .
        '<span class="error" id="passwordErr">' . $passwordErr . '</span>' .
        '</div>' .
        '<div class="fieldContainer">' .
        '<label for="ripPassword">Ripeti Password:</label>' .
        '<input type="password" id="ripPassword" name="ripPassword" onkeyup="matchPassword()" value="' . $datiUtente["password"] . '"">' .
        '<span class="error" id="ripPasswordErr">' . $ripPasswordErr . '</span>' .
        '</div>' .
        '<div class=containerButton>' .
        '<button type="button" id="conferma" onclick="submitForm()">Conferma</button>' .
        '</div>' .
        '</form>';

    return $str;
}
function creaElencoLavori($dati, $idSel)
{
    $listaLavori = "";
    $url = "./lavori.php?op=VIS";
    $tmpLi = '<li %s><a href="%s" title="Visualizza le informazioni">%s</a></li>';

    if (count($dati) > 0) {
        $listaLavori .= '<ul>';


        foreach ($dati as $item) {
            $tmpCls = ($idSel == $item["id"]) ? ' class="selezionato" ' : '';
            $tmpUrl = $url . "&idSel=" . $item["id"];
            $listaLavori .= sprintf($tmpLi, $tmpCls, $tmpUrl, $item["nome"]);
        }

        $listaLavori .= '</ul>';
    }
    return $listaLavori;
}
function creaFormVisualizzazioneLavori($datiLavori)
{
    $str =
        '<span class="grass">Nome:</span><div class="datiLavori"> ' . $datiLavori["nome"] . '</div>  ' .
        '<span class="grass">Descrizione:</span><div class="datiLavoriDescrizione"> ' . $datiLavori["descrizione"] . '</div> ' .
        '</div>';
    return $str;
}
function creaTitoloLavori($datiLavori)
{
    $str = '<h3 class="titolo">' . $datiLavori['nome'] . '</h3>';
    return $str;
}
function creaFormModificaLavori($datiLavori, $nomeErr, $msg)
{
    $str = '<form action="" method="post" id="formModificaLavori" >' .
        '<label for"nome">Nome:</label>' .
        '<input type="text" id="nome" name="nome" value="' . $datiLavori["nome"] . '">' .
        '<span class="error" id="errNome">' .
        $nomeErr .
        '</span>' .
        '<label for"descrizione">Descrizione:</label>' .
        '<textarea class="descrizione" name="descrizione" rows="7" cols="40" maxlength="500">' . $datiLavori["descrizione"] . '</textarea>' .
        ' <script>
        //////////////// CONTATORE CARATTERI RIMANENTI ////////////////////////
 
        window.addEventListener("load", () => {
            for (let textarea of document.querySelectorAll("textarea.descrizione")) {
                //Creo il contenitore per la text e il contatore
                let wrap = document.createElement("div");
                wrap.className = "textCountContainer";
                textarea.parentNode.insertBefore(wrap, textarea);
                wrap.appendChild(textarea);

                // Inserisco il contatore
                let counter = document.createElement("div");
                counter.className = "counter";
                counter.innerHTML = textarea.maxLength;
                wrap.appendChild(counter);


                // Calcolo i caratteri rimanenti durante la digitiazione
                textarea.addEventListener("keyup", () => counter.innerHTML = textarea.maxLength - textarea.value.length + (" Caratteri rimanenti"));
                counter.innerHTML = counter.innerHTML + " Caratteri rimanenti";
            }
        });
        //////////////////////////////////////////////////////////////////////////

        //////////////////////FORM VALIDATION LATO CLIENT/////////////////////////////////////////////////////////
        //controllo lato client se i campi sono stati compilati correttamente
        function submitForm(){
            const form = document.getElementById("formModificaLavori");
            let nome = document.getElementById("nome");
            let errNome = document.getElementById("errNome");
            let btnConferma = document.getElementById("conferma");
            let success = 0;
            //Verifico che il campo nome non sia vuoto
            if (nome.value == "") {
                nome.style.borderColor = "#ff0000b0";
                errNome.innerHTML = "Campo obbligatorio";
            }else {
                nome.style.borderColor = "#afafaf";
                errNome.innerHTML ="";
                //controllo che la lunghezza del campo sia corretta
                if (nome.value.length < 3 || nome.value.lenght > 20) {
                nome.style.borderColor = "#ff0000b0";
                errNome.innerHTML = "La lunghezza deve essere compresa tra 3 e 20 caratteri";
                } else {
                nome.style.borderColor = "#afafaf";
                errNome.innerHTML ="";
                btnConferma.onlick="";
                btnConferma.type= "submit";
                }
            }
        }
    </script>' .
        '<div class="buttonContainer">' .
        '<button type="button" id="conferma" name="conferma" form="formModificaLavori" onclick="submitForm()">Conferma</button>' .
        '</div>' .
        '</form>' .

        '<form action="" method="post" id="formUpload" enctype="multipart/form-data">' .
        '<div>Inserisci immagine</div>' .
        '<input type="file" id="file" name="uploadFile" onchange="FileValidation()">' .
        '<span class="error" id="messaggio">' . $msg . '</span>' .
        '<div class="buttonContainer">' .
        '<button type="submit" name="upload" id="btnUpload" disabled>Carica</button>' .
        '<button type="submit" name="elimina" id="btnElimina">Elimina</button>' .
        '</div>' .
        '<div class="imageContainer">';

    if (file_exists('./uploads/' . $datiLavori["id"] . '/' . $datiLavori["immagine"])) {
        $str .= '<img src="./uploads/' . $datiLavori["id"] . '/' . $datiLavori["immagine"] . '" alt="' . $datiLavori["nome"] . '"></img >';
    } else {
        $str .= '<img src="./img/default-image.png" alt="' . $datiLavori["nome"] . '"></img >';
    }
    $str .= '</div>' .
        '<script>
        //////////////////////FILE VALIDATION LATO CLIENT/////////////////////////////////////
        function FileValidation(){
            const file = document.getElementById("file");
            let msgErr = document.getElementById("messaggio");
            let btnUpload = document.getElementById("btnUpload");
            let success = 0;
            //Controllo se è un formato valido
            if (file.files[0].type != "image/jpg" && file.files[0].type != "image/jpeg" && file.files[0].type != "image/png" && file.files[0].type != "image/gif"){
                msgErr.innerHTML = "Sono ammessi solo i formati: JPG, JPEG, PNG & GIF";
                console.log(file.files[0].type);
                btnUpload.disabled = true;
            }else{
                success ++;
                msgErr.innerHTML = "";
            }
            // Controllo se il file è inferiore a 5mb
            if (file.files[0].size > 5000000) { 
                msgErr.innerHTML = "Il file deve essere inferiore a 5Mb";
                btnUpload.disabled = true;
            } else{
                success ++;
                msgErr.innerHTML = "";
            }
            if (success==2) {
            btnUpload.disabled = false;
            btnUpload.style.cursor = pointer;
            }
           
        }
        </script>' .
        '</form>';


    return $str;
}
function creaFormAggiungiLavori($nomeErr)
{
    $str = '<form action="" method="post" id="formAggiungiLavori">' .
        '<input type="text" id="nome" placeholder="Nome *" name="nome" value="' . $_POST["nome"] . '">' .
        '<span class="error" id="errNome">' .
        $nomeErr .
        '</span>' .
        '<textarea class="descrizione" name="descrizione" rows="7" cols="40" maxlength="500" placeholder="Descrizione">' . $_POST["descrizione"] . '</textarea>' .
        ' <script>
        //////////////// CONTATORE CARATTERI RIMANENTI ////////////////////////
 
        window.addEventListener("load", () => {
            for (let textarea of document.querySelectorAll("textarea.descrizione")) {
                //Creo il contenitore per la text e il contatore
                let wrap = document.createElement("div");
                wrap.className = "textCountContainer";
                textarea.parentNode.insertBefore(wrap, textarea);
                wrap.appendChild(textarea);

                // Inserisco il contatore
                let counter = document.createElement("div");
                counter.className = "counter";
                counter.innerHTML = textarea.maxLength;
                wrap.appendChild(counter);


                // Calcolo i caratteri rimanenti durante la digitiazione
                textarea.addEventListener("keyup", () => counter.innerHTML = textarea.maxLength - textarea.value.length + (" Caratteri rimanenti"));
                counter.innerHTML = counter.innerHTML + " Caratteri rimanenti";
            }
        });
        //////////////////////////////////////////////////////////////////////////

        //////////////////////FORM VALIDATION LATO CLIENT/////////////////////////////////////////////////////////
        //controllo lato client se i campi sono stati compilati correttamente
        function submitForm(){
            const form = document.getElementById("formAggiungiLavori");
            let nome = document.getElementById("nome");
            let errNome = document.getElementById("errNome");
            let success = 0;
            //Verifico che il campo nome non sia vuoto
            if (nome.value == "") {
                nome.style.borderColor = "#ff0000b0";
                errNome.innerHTML = "Campo obbligatorio";
            }else {
                errNome.innerHTML = "";
                nome.style.borderColor = "#afafaf";
                success ++;
                //controllo che la lunghezza del campo sia corretta
                if (nome.value.length < 3 || nome.value.lenght > 20) {
                    nome.style.borderColor = "#ff0000b0";
                    errNome.innerHTML = "La lunghezza deve essere compresa tra 3 e 20 caratteri";
                } else {
                    success ++;
                    errNome.innerHTML = "";
                    nome.style.borderColor = "#afafaf";
                }
            }
           
            if (success == 2){
                form.submit();
                success=0;
            }
        }

    </script>' .
        '<button type="button" name="btnSubmit" form="formAggiungiLavori" onclick="submitForm()">Conferma</button>' .

        '</form>';

    return $str;
}

function delete_directory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}
