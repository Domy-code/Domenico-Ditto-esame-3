<?php
require_once("funzioni.php");
echo creaInizioPagina();
echo creaMenuNavbar();


?>

<section class="rigaPresentazione">

    <div class="colonnaLarga">

        <h1>
            Ciao,
            <br>
            sono Domenico
        </h1>



        <p>
            A full stack web developer, magna deserunt mollit in aliquip sint ullamco eiusmod aliqua Lorem
            elit Lorem. Incididunt sunt
            veniam
            dolor quis eiusmod. Excepteur voluptate cillum voluptate aliqua culpa elit non cillum irure
            officia esse
            anim ad culpa. Voluptate adipisicing labore in dolore sint laborum velit laborum pariatur
            fugiat.

        </p>

    </div><!--.colonnaLarga-->

    <div class="colonnaStretta">


    </div><!--.colonnaStretta-->

</section><!--.rigaPresentazione-->

<!-- Riga About -->

<section id="rigaAbout">

    <div class="colonnaStretta">

    </div>

    <div class="colonnaLarga">

        <h3>ABOUT</h3>

        <p>
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Soluta veritatis libero illum provident.
            Ducimus dolores vitae eaque expedita nostrum, aperiam magnam dolorem adipisci provident voluptatum enim
            repudiandae, doloremque, ullam nisi?
            Eaque fugit, dignissimos maiores provident excepturi consectetur laboriosam delectus, perspiciatis
            ipsum, ut assumenda doloribus quia animi est? Quibusdam eligendi voluptatibus, quia ab nobis, hic magnam
            alias provident perferendis saepe temporibus.
            Dignissimos neque molestiae non sunt explicabo nesciunt alias, accusamus cumque numquam nostrum, quos
            perspiciatis, impedit illum eveniet eaque unde incidunt excepturi omnis dolorum eius cupiditate. Et,
            aliquid repellat? Soluta, asperiores?
            Soluta excepturi sunt ipsa eius debitis magnam laboriosam vitae pariatur ab qui non, libero natus, quos
            ratione accusamus autem minus suscipit commodi maiores? Libero culpa omnis modi odio unde ex?
            Veritatis nobis accusamus perspiciatis autem, repellat repudiandae, veniam quo impedit pariatur deserunt
            magni cumque velit libero dolores maiores beatae dolore in facere nihil tenetur. Voluptas, aspernatur
            nisi. Voluptas, esse qui!
        </p>

        <ul class="gallerySkills--">
            <li>
                <img src="./img/figma.png" alt="Figma">
            </li>

            <li>
                <img src="./img/adobe-photoshop.png" alt="Photoshop">
            </li>

            <li>
                <img src="./img/html.png" alt="html">
            </li>

            <li>
                <img src="./img/css-3.png" alt="css">
            </li>

            <li>
                <img src="./img/sass.png" alt="sass">
            </li>

            <li>
                <img src="./img/php.png" alt="php">
            </li>

            <li>
                <img src="./img/js.png" alt="js">
            </li>

            <li>
                <img src="./img/sql-server.png" alt="sql">
            </li>


        </ul>

    </div><!--.colonnaLarga-->
</section><!--.rigaAbout-->

<!-- Riga Services -->

<section id="rigaServices">

    <div class="colonnaLarga">

        <h3>SERVIZI</h3>

        <p>
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Soluta veritatis libero illum provident.
            Ducimus dolores vitae eaque expedita nostrum, aperiam magnam dolorem adipisci provident voluptatum enim
            repudiandae, doloremque, ullam nisi?
        </p>
        <ul>
            <li><img src="./img/ux-design.png" alt="UX e UI design"><strong>UX E UI DESIGN</strong><br>
            <li><img src="./img/graphic-design.png" alt="Progettazione"><strong>PROGETTAZIONE</strong><br>
            <li><img src="./img/web-design.png" alt="front-end"><strong>FRONT-END</strong><br>
            <li><img src="./img/development.png" alt="Back-end"><strong>BACK-END</strong><br>
            <li><img src="./img/tools.png" alt="Manutenzione"><strong>MANUTENZIONE</strong><br>
            <li><img src="./img/technical-support.png" alt="Supporto"><strong>SUPPORTO</strong><br>
        </ul>

    </div><!--.colonnaLarga-->

    <div class="colonnaStretta">
    </div>

</section><!--.rigaServices-->

<!-- Riga works -->

<section id="rigaWorks">

    <div class="rigaStretta">
    </div><!--.rigaStretta-->

    <div class="colonnaLarga">
        <h3>LAVORI</h3>
        <div class="gallery">
            <ul>
                <?php
                echo creaGalleryLavori();
                ?>
            </ul>

        </div><!--.gallery-->
    </div><!--colonnaLarga-->
</section><!--.rigaWorks-->

<!-- Riga contacts -->

<section id="rigaContatti">
    <div class="colonnaStretta">
    </div>
    <div class="colonnaLarga">
        <div class="rigaTitolo">
            <h3>CONTATTI</h3>
        </div>
        <div class="rigaContenuto">

            <div class="contatti">
                <ul>
                    <!-- <li>

                        <img src="./img/placeholder.png" alt="Indirizzo" title="Indirizzo">

                        <address>
                            <a href="https://goo.gl/maps/rEJ62W3ydmdE9z5x5" title="indirizzo">Via di casa, 1 <br>Roma<br>Italia</a>
                        </address>

                    </li> -->

                    <li>
                        <img src="./img/mail.png" alt="mail" title="Mail">
                        <a href="mailto:domenicoditto@gmail.com" title="Scrivimi una mail">domenicoditto@gmail.com</a>
                    </li>

                    <li>
                        <img src="./img/whatsapp.png" alt="telefono" title="Telefono">
                        <a href="tel:+393483221662" title="Chiamami">+39 3483221662</a>
                    </li>
                </ul>
            </div><!--.Contatti-->

            <?php
            ///////////////////FORM VALIDATION LATO SERVER/////////////////////////////////////////////

            // Definisco le variabli e setto il valore su vuoto
            $nomeErr = $emailErr = $telefonoErr = $messaggioErr = "";
            $nome = $email = $telefono = $messaggio = $check = "";
            $success = "";
            $inviato = false;

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                //Controllo se il campo sia stato compilato
                if (empty($_POST["nome"])) {
                    $nomeErr = "*Campo obbligatorio";
                    //controllo che la lunghezza del campo sia corretta
                } else if (strlen($_POST["nome"]) < 3 || strlen($_POST["nome"]) > 20) {
                    $nomeErr = "La lunghezza deve essere compresa tra 3 e 20 caratteri";
                } else {
                    $nome = $_POST["nome"];
                    // controllo se il nome contiene solo lettere e spazi vuoti
                    if (!preg_match("/^[a-zA-Z-' ]*$/", $nome)) {
                        $nomeErr = "Sono ammessi solo lettere e spazi";
                        $nome = "";
                    } else {
                        $success++;
                    }
                }

                //Controllo se il campo sia stato compilato
                if (empty($_POST["email"])) {
                    $emailErr = "*Campo obbligatorio";
                } else {
                    $email = $_POST["email"];
                    // Controllo se la mail sia scritta in un formato valido
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emailErr = "Formato email non valido";
                        $email = "";
                    } else {
                        $success++;
                    }
                }
                //Controllo se il campo sia stato compilato
                if (empty($_POST["telefono"])) {
                    $telefonoErr = "*Campo obbligatorio";
                } else {
                    $telefono = $_POST["telefono"];
                    // Controllo se il campo telefono contiene solo numeri 
                    if (strlen($_POST["telefono"]) < 4 || strlen($_POST["telefono"]) > 12) {

                        $telefonoErr = "La lunghezza deve essere compresa tra 4 e 12 caratteri";
                    }
                    if (!preg_match("/^[0-9-' ]*$/", $telefono)) {
                        $telefonoErr = "Sono ammessi solo numeri";
                        $telefono = "";
                    } else {
                        $success++;
                    }
                }
                //Controllo se il campo sia stato compilato
                if (empty($_POST["messaggio"])) {
                    $messaggioErr = "*Campo obbligatorio";
                } else {
                    $messaggio = $_POST["messaggio"];
                    $success++;
                }
                echo '<script>
                window.onload = function() {
                 window.location.replace("/index.php#rigaContatti");
             } </script>';
                //Scrivo i dati sul database e resetto i campi
                if ($success == 4) {
                    require_once('dati.php');
                    try {
                        $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);

                        $sql = "INSERT INTO contacts(nome, telefono, email, messaggio) VALUES(:nome, :telefono, :email, :messaggio);";
                        $query = $pdo->prepare($sql);
                        $data = [
                            ':nome' => $nome,
                            ':telefono' => $telefono,
                            ':email' => $email,
                            ':messaggio' => $messaggio,
                        ];
                        $query->execute($data);
                        $nome = "";
                        $telefono = "";
                        $email = "";
                        $messagio = "";
                        $inviato = true;
                    } catch (PDOException $e) {
                        exit("<br><br>Errore PDO: " . $e->getMessage());
                    }
                }
            }
            ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="formContatti">
                <input id="cntctNome" type="text" name="nome" value="<?php echo $nome; ?>" placeholder="Nome*">
                <span class="error" id="errNome"><?php echo $nomeErr; ?></span>

                <input type="tel" name="telefono" id="telefono" value="<?php echo $telefono; ?>" placeholder="Telefono*">
                <span class="error" id="errTelefono"><?php echo $telefonoErr; ?></span>

                <input type="text" id ="email" name="email" value="<?php echo $email; ?>" placeholder="E-mail*">
                <span class="error" id="errEmail"><?php echo $emailErr; ?></span>

                <textarea class="messaggio" id="messaggio" name="messaggio" rows="5" cols="40" placeholder="Lascia un messaggio*" value="<?php echo $messaggio; ?>" maxlength="500"></textarea>
                <span class="error" id="errMessaggio"><?php echo $messaggioErr; ?></span>
                <script>
                    //////////////// CONTATORE CARATTERI RIMANENTI ////////////////////////
                    window.addEventListener("load", () => {
                        for (let textarea of document.querySelectorAll("textarea.messaggio")) {
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
                </script>
                <div class="containerPrivacy">
                    <label for="privacyCheck" class=textPrivacy>
                        Ho preso visione della <a href="https://www.iubenda.com/privacy-policy/80605050.pdf">privacy policy</a> e acconsento al trattamento dei dati personali.
                    </label>
                    <input type="checkbox" id="privacyCheck" name="privacyCheck">
                </div>
                <span class="error" id="privacyError"></span>
                <div class="btnContainer">
                    <div class="overButton" id="overButton" onclick="privacyMsgError()"></div>
                    <button type="button" id="btnContatti" name="btnSubmit" value="Submit" formaction="#formContatti" onclick="submitForm()" disabled>Invia</button>
                </div>
                <script type="application/javascript">
                   
                    var checkbox = document.querySelector("input[name=privacyCheck]");
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            document.getElementById("btnContatti").disabled = false;
                            document.getElementById("btnContatti").style.cursor = 'pointer';
                            document.getElementById("overButton").style.zIndex = "0";
                            document.getElementById("privacyError").innerHTML = "";
                        } else {
                            document.getElementById("btnContatti").disabled = true;
                            document.getElementById("btnContatti").style.cursor = 'not-allowed';
                            document.getElementById("overButton").style.zIndex = "1";
                        }

                    });
                </script>
                <?php
                if ($inviato == true) {
                    echo "<span class= messaggioInviato ><br>Il messaggio è stato inviato correttamente.<br>Sarai ricontattato al più presto</span>";
                    $inviato = false;
                }
                ?>

            </form>

            <script>
                //////////////////////FORM VALIDATION LATO CLIENT/////////////////////////////////////////////////////////
                //controllo lato client se i campi sono stati compilati correttamente
                function submitForm() {
                    const form = document.getElementById('formContatti');
                    let nome = document.getElementById("cntctNome");
                    let errNome = document.getElementById("errNome");
                    let success = 0;
                    let regexNome = /^[a-zA-Z-' ]*$/;
                    let telefono = document.getElementById("telefono");
                    let errTelefono = document.getElementById("errTelefono");
                    let regexTelefono = /^[0-9-' ]*$/;
                    let email =document.getElementById("email");
                    let errEmail =document.getElementById("errEmail");
                    let regexEmail =  /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                    let messaggio = document.getElementById("messaggio");
                    let errMessaggio = document.getElementById("errMessaggio");



                    //Verifico che il campo nome non sia vuoto
                    if (nome.value == "") {
                        nome.style.borderColor = "#ff0000b0";
                        errNome.innerHTML = "Campo obbligatorio";
                    }
                    // controllo se il nome contiene solo lettere e spazi vuoti
                    else if (!regexNome.test(nome.value)) {
                        nome.style.borderColor = "#ff0000b0";
                        errNome.innerHTML= "Sono ammesse solo lettere e spazi";
                    }
                    //controllo che la lunghezza del campo sia corretta
                    else if (nome.value.length < 3 || nome.value.lenght > 20) {
                        nome.style.borderColor = "#ff0000b0";
                        errNome.innerHTML = "La lunghezza deve essere compresa tra 3 e 20 numeri";
                    } else {
                        success ++;
                        errNome.innerHTML="";
                        nome.style.borderColor= "#afafaf";
                    }


                    //Verifico che il campo non sia vuoto
                    if (telefono.value == "") {
                        telefono.style.borderColor = "#ff0000b0";
                        errTelefono.innerHTML = "Campo obbligatorio";
                    }
                    // controllo se il campo contiene solo numeri
                    else if (!regexTelefono.test(telefono.value)) {
                        telefono.style.borderColor = "#ff0000b0";
                        errTelefono.innerHTML = "Sono ammessi solo numeri";
                    }
                    //controllo che la lunghezza del campo sia corretta
                    else if (telefono.value.length < 4 || telefono.value.lenght > 12) {
                        telefono.style.borderColor = "#ff0000b0";
                        errTelefono.innerHTML = "La lunghezza deve essere compresa tra 4 e 12 numeri";
                    } else {
                        success ++;
                        errTelefono.innerHTML="";
                        telefono.style.borderColor= "#afafaf";
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
                        success ++;
                        errEmail.innerHTML="";
                        email.style.borderColor= "#afafaf";
                    }
                     //Verifico che il campo non sia vuoto
                     if (messaggio.value == "") {
                        messaggio.style.borderColor = "#ff0000b0";
                        errMessaggio.innerHTML = "Campo obbligatorio";
                    }else {
                        success ++;
                        errMessaggio.innerHTML="";
                        messaggio.style.borderColor= "#afafaf";
                    }
                    // Se non ci sono errori proseguo col submit del form
                    if (success == 4) {
                        form.submit();
                        success = 0;
                    } 
                }
            </script>


        </div>
    </div>
</section><!--rigaContatti-->
<?php
echo creaFinePagina();
