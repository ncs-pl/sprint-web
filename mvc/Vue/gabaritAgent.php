<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Sprint Bank | Agent</title>
    <!--<link rel="stylesheet" href="Vue/css/styleAgent.css">-->
    <meta charset="utf-8">
</head>
<body>
<div class="navbar">
    <div class="infos">
        <?php if (! empty($contenu)) {
            echo $contenu;
        } ?>
    </div>
    <a href="?action2=gestion_clients">
        <div class="item">Modifier client</div>
    </a>
    <div class="item">Synthèse client</div>
    <div class="item">Effectuer opération</div>
    <div class="item">Gestion RDV</div>
    <a href="sprintBank.php">
        <div class="logout">Déconnexion</div>
    </a>
</div>
</body>
</html>

