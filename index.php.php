<?php

function getFormData($fieldName, $default = "") {
    return isset($_POST[$fieldName]) ? htmlspecialchars(trim($_POST[$fieldName])) : $default;
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = getFormData("nom");
    $prenom = getFormData("prenom");
    $telephone = getFormData("telephone");
    $montant = filter_var($_POST["montant"], FILTER_VALIDATE_FLOAT);
    $acompte = filter_var($_POST["acompte"], FILTER_VALIDATE_FLOAT);
    $nombre_versements = filter_var($_POST["versements"], FILTER_VALIDATE_INT);
    $date_debut = getFormData("date_debut");

    if (empty($nom)) {
        $errors[] = "Le nom est requis.";
    }
    if (empty($prenom)) {
        $errors[] = "Le prénom est requis.";
    }
    if (empty($telephone)) {
        $errors[] = "Le téléphone est requis.";
    }
    if ($montant === false || $montant <= 0) {
        $errors[] = "Le montant du prêt doit être un nombre positif.";
    }
    if ($acompte === false || $acompte < 0) {
        $errors[] = "L'acompte doit être un nombre positif ou zéro.";
    }
    if ($nombre_versements === false || $nombre_versements <= 0) {
        $errors[] = "Le nombre de versements doit être un entier positif.";
    }
    if (!validateDate($date_debut)) {
        $errors[] = "La date de début doit être au format AAAA-MM-JJ.";
    }
    if ($acompte > $montant) {
        $errors[] = "L'acompte ne peut pas être supérieur au montant du prêt.";
    }

    if (empty($errors)) {
        $solde = $montant - $acompte;
        $mensualite = $solde / $nombre_versements;

        echo "<h2>Échéancier de Remboursement</h2>";
        echo "<p>Nom : " . htmlspecialchars($nom) . "</p>";
        echo "<p>Prénom : " . htmlspecialchars($prenom) . "</p>";
        echo "<p>Téléphone : " . htmlspecialchars($telephone) . "</p>";
        echo "<p>Montant du prêt : " . number_format($montant, 2) . "</p>";
        echo "<p>Acompte initial : " . number_format($acompte, 2) . "</p>";
        echo "<p>Nombre de versements : " . $nombre_versements . "</p>";
        echo "<p>Solde à rembourser : " . number_format($solde, 2) . "</p>";
        echo "<p>Mensualité : " . number_format($mensualite, 2) . "</p>";

        echo "<table border='1'>";
        echo "<tr><th>Date</th><th>Mensualité</th><th>Solde restant</th></tr>";
        
        for ($i = 0; $i < $nombre_versements; $i++) {
            $date_courante = date('Y/m/d', strtotime("+" . $i . " month", strtotime($date_debut)));
            $solde_restant = $solde - ($mensualite * $i);
            if ($solde_restant < 0) {
                $solde_restant = 0;
            }
            echo "<tr>";
            echo "<td>" . date('d/m/Y', strtotime($date_courante)) . "</td>";
            echo "<td>" . number_format($mensualite, 2) . "</td>";
            echo "<td>" . number_format($solde_restant, 2) . "</td>";
            echo "</tr>";
        }

        echo "</table>";

    } else {
        echo "<p style='color:red;'>Saisie invalide. Veuillez corriger les erreurs suivantes :</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }

} else {
    echo "<p>Veuillez remplir le formulaire pour calculer l'échéancier de remboursement.</p>";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculateur d'Échéancier de Remboursement</title>
    <style>
        /* Styles de base pour une meilleure apparence */
        label { display: block; margin-bottom: 5px; }
        input { width: 200px; padding: 5px; margin-bottom: 10px; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .error { color: red; } /* Style for error messages */
    </style>
</head>
<body>

<?php echo "<br><br><h2>Exemple d'Échéancier</h2>";

    echo "<table border='1'>";
    echo "<tr><th>Date</th><th>Mensualité</th><th>Solde restant</th></tr>";
    echo "<tr><td>07/11/2020</td><td>496.67</td><td>6953.38</td></tr>";
    echo "<tr><td>07/12/2020</td><td>496.67</td><td>6456.71</td></tr>";
    echo "<tr><td>07/01/2021</td><td>496.67</td><td>5960.04</td></tr>";
    echo "<tr><td>07/02/2021</td><td>496.67</td><td>5463.37</td></tr>";
    echo "<tr><td>07/03/2021</td><td>496.67</td><td>4966.70</td></tr>";
    echo "<tr><td>07/04/2021</td><td>496.67</td><td>4470.03</td></tr>";
    echo "<tr><td>07/05/2021</td><td>496.67</td><td>3973.36</td></tr>";
    echo "<tr><td>07/06/2021</td><td>496.67</td><td>3476.69</td></tr>";
    echo "<tr><td>07/07/2021</td><td>496.67</td><td>2980.02</td></tr>";
    echo "<tr><td>07/08/2021</td><td>496.67</td><td>2483.35</td></tr>";
    echo "<tr><td>07/09/2021</td><td>496.67</td><td>1986.68</td></tr>";
    echo "<tr><td>07/10/2021</td><td>496.67</td><td>1490.01</td></tr>";
    echo "<tr><td>07/11/2021</td><td>496.67</td><td>993.34</td></tr>";
    echo "<tr><td>07/12/2021</td><td>496.67</td><td>496.67</td></tr>";
    echo "<tr><td>07/01/2022</td><td>496.67</td><td>0.00</td></tr>";
    echo "</table>";
    ?>

    <h2>Calculateur d'Échéancier de Remboursement</h2>

    <form action="" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required value="<?php echo getFormData('nom'); ?>">

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required value="<?php echo getFormData('prenom'); ?>">

        <label for="telephone">Téléphone :</label>
        <input type="text" id="telephone" name="telephone" required value="<?php echo getFormData('telephone'); ?>">

        <label for="montant">Montant du prêt :</label>
        <input type="text" id="montant" name="montant" required value="<?php echo getFormData('montant'); ?>">

        <label for="acompte">Acompte initial :</label>
        <input type="text" id="acompte" name="acompte" required value="<?php echo getFormData('acompte'); ?>">

        <label for="versements">Nombre de versements mensuels :</label>
        <input type="number" id="versements" name="versements" min="1" required value="<?php echo getFormData('versements'); ?>">

        <label for="date_debut">Date de début du prêt :</label>
        <input type="date" id="date_debut" name="date_debut" required value="<?php echo getFormData('date_debut'); ?>">

        <input type="submit" value="Calculer l'Échéancier">
    </form>

<button onclick="clearTable()">Effacer le tableau</button>

<script>
function clearTable() {
    let table = document.querySelector("table");
    if (table) {
        table.innerHTML = ""; 
    }
}
</script>

</body>
</html>