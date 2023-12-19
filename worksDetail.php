<?php
require_once("funzioni.php");
echo creaInizioPagina();
echo creaMenuNavbar();
$idSel = htmlspecialchars($_GET['idSel']);

try {
    $pdo = new PDO("mysql:host=" . INDIRIZZO . ";dbname=" . DB, UTENTE, PASSWORD);
    $sql = "SELECT 
                id,
                nome,
                immagine,
                descrizione
                FROM db39opf4ki7vt9.lavori;";
    $query = $pdo->prepare($sql);
    $query->execute();
    if ($query->rowCount() > 0) {
        while ($righe = $query->fetch(PDO::FETCH_ASSOC)) {
            $tmp = array(
                "id" => $righe["id"],
                "nome" => $righe["nome"],
                "immagine" => $righe["immagine"],
                "descrizione" => $righe["descrizione"]
            );
            $dati[] = $tmp;

            if ($idSel != null && $idSel == $tmp["id"]) {
                $datiLavori = $tmp;
            }
        }
        if ($idSel == null) {
            $idSel = $dati[0]["id"];
            $datiLavori = $dati[0];
        }
    }
    echo($datiLavori["image"]);
} catch (PDOException $e) {
    echo "<br><br>Errore PDO: " . $e->getMessage();
}
?>

    <section class="rigaLavori">

       
        <div class="rigaDescrizione">

            <h1><?php echo($datiLavori["nome"])?></h1>

            <div class="rigaFoto">
            <img src=<?php echo './uploads/'.$datiLavori["id"].'/'.$datiLavori["immagine"]?> alt="work">
        </div>


            <p><?php echo($datiLavori["descrizione"])?></p>
            <br>

            <div class="button">
                <button onclick="document.location='./index.php#rigaContatti'" title="Clicca per contattarmi">Contattami</button>
            </div>
        </div>




    </section><!--.rigaLavori-->
    <?php
    echo creaFinePagina();
    ?>