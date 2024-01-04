<?php

require_once 'Modele/connect.php';

function getConnexion()
{
    $connexion = new PDO('mysql:host='.SERVEUR.';dbname='.BDD, USER, PASSWORD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connexion->query('SET NAMES UTF8');

    return $connexion;
}

function verifierLogin($usr, $mdp)
{
    $connexion = getConnexion();
    $requete = "select login,mdp,nom,prenom,type from employe where login='$usr' and mdp='$mdp'";
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $ligne = $resultat->fetch();
    $resultat->closeCursor();

    return $ligne;
}

function verifierAvantAjout($nom, $prenom, $login)
{
    $connexion = getConnexion();
    $requete = "select nom,prenom from employe where nom='$nom' and prenom='$prenom'";
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $verifPersonne = $resultat->fetch();
    $resultat->closeCursor();
    $requete = "select login from employe where login='$login'";
    $resultat = $connexion->query($requete);
    $verifLogin = $resultat->fetch();
    $resultat->closeCursor();
    $ensemble = ['personne' => $verifPersonne, 'login' => $verifLogin];

    return $ensemble;
}

function ajouterEmploye($nom, $prenom, $login, $mdp, $dateEmbauche, $type)
{
    $connexion = getConnexion();
    $requete = "INSERT INTO employe (NOM, PRENOM, LOGIN, MDP, DATEEMBAUCHE, TYPE)
    VALUES ('$nom', '$prenom', '$login', '$mdp', '$dateEmbauche', '$type')";
    $resultat = $connexion->query($requete);
    $resultat->closeCursor();
}

function modifierEmploye($login, $mdp, $nom, $prenom)
{
    $connexion = getConnexion();
    $requete = "UPDATE employe SET  login = '$login', mdp = '$mdp' WHERE nom='$nom' and prenom='$prenom'";
    $resultat = $connexion->query($requete);
    $resultat->closeCursor();
}

function mdlGetAllMotif()
{

    $connexion = getConnexion();

    $requete = 'SELECT * FROM `motif`;';
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $motif = $resultat->fetchAll();
    $resultat->closeCursor();

    return $motif;
}

function mdlModifierPiece($id, $value)
{

    $connexion = getConnexion();

    $requete = 'UPDATE motif SET justificatifs = "'.$value.'" WHERE id = '.intval($id);
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $resultat->fetchAll();
}

function mdlGetAllTypeAccount()
{

    $connexion = getConnexion();

    $requete = 'SELECT * FROM typecompte;';
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $typeAccount = $resultat->fetchAll();
    $resultat->closeCursor();

    return $typeAccount;
}

function mdlGetAllTypeContract()
{

    $connexion = getConnexion();

    $requete = 'SELECT * FROM typecontrat;';
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $typeContract = $resultat->fetchAll();
    $resultat->closeCursor();

    return $typeContract;
}

function mdlGetTypeByName($name, $type)
{

    $connexion = getConnexion();

    $requete = '';
    if ($type == 'account') {
        $requete = 'SELECT * FROM typecompte WHERE nom = "'.$name.'";';
    } elseif ($type == 'contract') {
        $requete = 'SELECT * FROM typecontrat WHERE nom = "'.$name.'";';
    } else {
        throw new Exception('Type non définie pour la requête GetTypeByName');
    }

    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $queryResult = $resultat->fetch();
    $resultat->closeCursor();

    return $queryResult;
}

function mdlAjouterType($nature, $nom, $pieceCreation, $pieceModification, $pieceSuppression)
{
    $connexion = getConnexion();

    $requete = 'INSERT INTO type'.$nature.'(nom) VALUES("'.$nom.'");
                INSERT INTO motif (libelle, justificatifs) VALUES
                ("Création '."d'un ".$nom.'","'.$pieceCreation.'"),
                ("Modification '."d'un ".$nom.'","'.$pieceModification.'"),
                ("Suppression '."d'un ".$nom.'","'.$pieceSuppression.'");';

    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $resultat->fetch();
    $resultat->closeCursor();
}

function mdlTypeIsAssign($id, $type)
{

    $connexion = getConnexion();

    $requete = '';
    if ($type == 'account') {
        $requete = 'SELECT * FROM estdetypecompte WHERE typeCompte = '.$id.';';
    } elseif ($type == 'contract') {
        $requete = 'SELECT * FROM estdetypecontrat WHERE typeContrat = '.$id.';';
    } else {
        throw new Exception('Type non définie pour la requête TypeIsAssign');
    }
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $verifIsAssign = $resultat->fetch();
    $resultat->closeCursor();

    return $verifIsAssign;
}

function mdlSupprimerType($id, $type)
{

    $connexion = getConnexion();

    $requete = '';
    if ($type == 'account') {
        $requete = 'DELETE FROM typecompte WHERE id = '.$id.';';
    } elseif ($type == 'contract') {
        $requete = 'DELETE FROM typecontrat WHERE id = '.$id.';';
    } else {
        throw new Exception('Type non définie pour la requête SupprimerType');
    }
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $resultat->fetch();
    $resultat->closeCursor();
}

function mdlGetTypeById($id, $type)
{

    $connexion = getConnexion();

    $requete = '';
    if ($type == 'account') {
        $requete = 'SELECT * FROM typecompte WHERE id = '.$id.';';
    } elseif ($type == 'contract') {
        $requete = 'SELECT * FROM typecontrat WHERE id = '.$id.';';
    } else {
        throw new Exception('Type non définie pour la requête GetTypeById');
    }

    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $queryResult = $resultat->fetch();
    $resultat->closeCursor();

    return $queryResult;
}

function mdlSupprimerMotif($name)
{

    $connexion = getConnexion();

    $requete = 'DELETE FROM motif WHERE libelle = "Création '."d'un ".$name.
                '" OR libelle = "Modification '."d'un ".$name.
                '" OR libelle = "Suppression '."d'un ".$name.'";';
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $resultat->fetch();
    $resultat->closeCursor();
}

function rechercheClient($nom, $prenom)
{
    $connexion = getConnexion();
    $requete = "select nom,prenom from client where nom='$nom' and prenom='$prenom'";
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $ligne = $resultat->fetchAll();

    return $ligne;
}

function modifierClient($champs, $valeur, $nom, $prenom)
{
    $connexion = getConnexion();
    $requete = "UPDATE client SET $champs= '$valeur' WHERE nom='$nom' and prenom='$prenom'";
    $resultat = $connexion->query($requete);
    $resultat->closeCursor();
}

function mdlGetClient($client)
{
    $connexion = getConnexion();

    $requete = 'SELECT * FROM client WHERE nom="'.$client['clientName'].'" AND prenom="'.$client['clientPrenom'].'" AND mail="'.$client['clientMail'].'"';
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $clientId = $resultat->fetch();
    $resultat->closeCursor();

    return $clientId;
}

function mdlInscriptionClient($client)
{
    $connexion = getConnexion();

    $requete = 'INSERT INTO
                client(nom, prenom, adresse, numTel, mail, profession, situation, dateAjout)
                VALUES
                ("'.$client['nom'].'", "'.$client['prenom'].'", "'.$client['adresse'].'", "'.$client['telephone'].'", "'.$client['email'].
                '", "'.$client['profession'].'", "'.$client['situation'].'", NOW())';
    $resultat = $connexion->query($requete);
    $resultat->closeCursor();
}

function mdlGetClientCompte($client)
{
    $connexion = getConnexion();

    $requeteVIEW = 'CREATE OR REPLACE VIEW CompteClient(idCompte) AS
                    SELECT compte.id FROM compte WHERE compte.id
                    IN (SELECT aouvert.compte FROM aouvert WHERE aouvert.client = '.$client.');

                    CREATE OR REPLACE VIEW CompteClientType(idCompte, typeCompte) AS
                    SELECT compteclient.idCompte, estdetypecompte.typeCompte FROM compteclient
                    INNER JOIN estdetypecompte ON estdetypecompte.compte = compteclient.idCompte;';

    $requete = 'SELECT compte.solde, compte.decouvert, compte.dateOuverture, typeCompte.nom FROM compte
                INNER JOIN compteclienttype ON compte.id = compteclienttype.idCompte
                INNER JOIN typeCompte ON typeCompte.id = compteclienttype.typeCompte;';

    $resultat = $connexion->query($requeteVIEW);
    $resultat->closeCursor();
    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $clientCompte = $resultat->fetchAll();
    $resultat->closeCursor();

    return $clientCompte;
}

function mdlGetContrat($client)
{
    $connexion = getConnexion();

    $requeteVIEW = 'CREATE OR REPLACE VIEW ContratClient(idContrat) AS
                    SELECT contrat.id FROM contrat WHERE contrat.id
                    IN (SELECT asouscrit.contrat FROM asouscrit WHERE asouscrit.client = '.$client.');

                    CREATE OR REPLACE VIEW ContratClientType(idContrat, typeContrat) AS
                    SELECT contratclient.idContrat, estdetypecontrat.typeContrat FROM contratclient
                    INNER JOIN estdetypecontrat ON estdetypecontrat.contrat = contratclient.idContrat;';

    $requete = 'SELECT contrat.tarifMensuel, contrat.dateOuverture, typecontrat.nom FROM contrat
                INNER JOIN contratclienttype ON contrat.id = contratclienttype.idContrat
                INNER JOIN typeContrat ON typeContrat.id = contratclienttype.typeContrat;';

    $resultat = $connexion->query($requeteVIEW);
    $resultat->closeCursor();

    $resultat = $connexion->query($requete);
    $resultat->setFetchMode(PDO::FETCH_OBJ);
    $clientContrat = $resultat->fetchAll();
    $resultat->closeCursor();

    return $clientContrat;
}
