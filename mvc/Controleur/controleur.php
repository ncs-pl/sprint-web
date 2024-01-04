<?php

require_once 'Modele/modele.php';
require_once 'Vue/vue.php';

//Page login
function ctrlPageLogin()
{
    pageLogin();
}

function ctrlVerifierId()
{
    $usr = $_POST['login'];
    $mdp = $_POST['mdp'];
    $ligne = verifierLogin($usr, $mdp);
    if ($ligne == false) {
        erreurId();
    } elseif ($ligne->type == 'DIRECTEUR') {
        pageDirecteur($ligne->nom, $ligne->prenom, $ligne->type);
    } elseif ($ligne->type == 'AGENT') {
        pageAgent($ligne->nom, $ligne->prenom, $ligne->type);
    } elseif ($ligne->type == 'CONSEILLER') {
        pageConseille($ligne->nom, $ligne->prenom, $ligne->type);
    }
}

//Gestion des employés
function ctrlGestion()
{
    pageGestion();
}

function ctrlAjouterEmploye()
{
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $login = $_POST['login'];
    $mdp = $_POST['mdp'];
    $dateEmbauche = $_POST['dateembauche'];
    $type = $_POST['poste'];
    $ensemble = verifierAvantAjout($nom, $prenom, $login);
    if ($ensemble['personne'] != false) {
        msgGestionEmployes('Personne déjà existante !');
    } elseif ($ensemble['login'] != false) {
        msgGestionEmployes('Login déjà utilisé !');
    } else {
        ajouterEmploye($nom, $prenom, $login, $mdp, $dateEmbauche, $type);
        msgGestionEmployes('Nouvel employé ajouté !');
    }
}

function ctrlModifierEmploye()
{
    $login = $_POST['login'];
    $mdp = $_POST['mdp'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $ensemble = verifierAvantAjout($nom, $prenom, $login);
    if ($ensemble['personne'] == false) {
        msgGestionEmployes('Aucun employé ne correspond à votre saisis.');
    } elseif ($ensemble['login'] != false) {
        msgGestionEmployes('Login déjà utilisé !');
    } else {
        modifierEmploye($login, $mdp, $nom, $prenom);
        msgGestionEmployes('Identifiants changés !');
    }
}

function ctrlGetAllMotif()
{
    $motif = mdlGetAllMotif();
    vueGetAllMotif($motif);
}

function ctrlModifierPiece($motif)
{
    if (isset($motif['modifier']) && isset($motif['valeurModifier'])) {

        if (strlen($motif['valeurModifier']) > 0) {

            $id = $motif['modifier'];
            $value = $motif['valeurModifier'];

            mdlModifierPiece($id, $value);

            vueMsgDirecteur('Le motif a bien été modifié');
        } else {
            throw new Exception("ctrlModifierPiece : motif['valeurModifier'] empty");
        }
    } else {
        throw new Exception("ctrlModifierPiece : motif['modifier'] or motif['valeurModifier'] not defined");
    }
}

//Comptes et contrats
function ctrlGetAllTypeAccountContract()
{
    $account = mdlGetAllTypeAccount();
    $contract = mdlGetAllTypeContract();

    vueGetAllTypeAccountContract($account, $contract);
}

function ctrlSupprimerTypeAccount($type)
{
    if (isset($type['account'])) {
        try {

            $result = mdlTypeIsAssign($type['account'], 'account');

            if ($result == false) {

                $name = mdlGetTypeById($type['account'], 'account')->nom;
                mdlSupprimerMotif($name);
                mdlSupprimerType($type['account'], 'account');
                vueMsgDirecteur('Le type de compte "'.$name.'" a bien été supprimé');
            } else {

                vueMsgDirecteur('Le type de compte ne peut être supprimé car il est assigné');
            }
        } catch (Exception $e) {
            vueMsgDirecteur($e->getMessage());
        }
    } elseif (isset($type['contract'])) {
        try {

            $result = mdlTypeIsAssign($type['contract'], 'contract');

            if ($result == false) {

                $name = mdlGetTypeById($type['contract'], 'contract')->nom;
                mdlSupprimerMotif($name);
                mdlSupprimerType($type['contract'], 'contract');
                vueMsgDirecteur('Le type de contrat "'.$name.'" a bien été supprimé');
            } else {
                vueMsgDirecteur('Le type de contrat ne peut être supprimé car il est assigné');
            }
        } catch (Exception $e) {
            vueMsgDirecteur($e->getMessage());
        }
    } else {
        throw new Exception('ctrlSupprimerTypeAccount : type["account"] not defined');
    }
}

function ctrlAjouterType($newType)
{

    if (
        strlen($newType['nom']) > 0 && strlen($newType['nature']) > 0 && strlen($newType['pieceCreation']) > 0 &&
        strlen($newType['pieceModification']) > 0 && strlen($newType['pieceSuppression']) > 0
    ) {

        $nom = $newType['nom'];
        $nature = $newType['nature'];
        $pieceCreation = $newType['pieceCreation'];
        $pieceModification = $newType['pieceModification'];
        $pieceSuppression = $newType['pieceSuppression'];

        if (mdlGetTypeByName($nom, 'account') == false
            && mdlGetTypeByName($nom, 'contract') == false) {

            mdlAjouterType($nature, $nom, $pieceCreation, $pieceModification, $pieceSuppression);

            vueMsgDirecteur('Le '.$nature.'"'.$nom.'" a bien été créer');
        } else {
            vueMsgDirecteur('Le nom "'.$nom.'" est déjà utilisé pour un type de compte ou contrat');
        }
    } else {
        throw new Exception("ctrlAjouterType : one of newType field not define or empty");
    }
}

//Agent -> Modification clients

function ctrlGestionClients()
{
    pageGestionClients();
}

function ctrlModifierClient()
{
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $ligne = rechercheClient($nom, $prenom);
    if ($ligne != false) {
        $changements = 'Changements effectués pour '.$nom.' '.$prenom.' : ';
        if (! empty($_POST['adresse'])) {
            modifierClient('adresse', $_POST['adresse'], $nom, $prenom);
            $changements .= '| Adresse |';
        }
        if (! empty($_POST['numtel'])) {
            modifierClient('numTel', $_POST['numtel'], $nom, $prenom);
            $changements .= '| Numéro de téléphone |';
        }
        if (! empty($_POST['email'])) {
            modifierClient('mail', $_POST['email'], $nom, $prenom);
            $changements .= '| Adresse mail |';
        }
        if (! empty($_POST['profession'])) {
            modifierClient('profession', $_POST['profession'], $nom, $prenom);
            $changements .= '| Profession |';
        }
        if (! empty($_POST['situation'])) {
            modifierClient('situation', $_POST['situation'], $nom, $prenom);
            $changements .= '| Situation |';
        }

        msgGestionClients('<p>
                    '.$changements.'</p>
                    <p><input type="submit" name="retour" value="Retour"></p>');
    } else {
        msgGestionClients('<p>
                Aucun client trouvé, vérifiez votre saisie.</p>
                <p><input type="submit" name="reessayer" value="Réessayer"></p>');
    }
}

function ctrlConseillerLoginClient()
{
    vueConseillerLoginClient();
}

function ctrlConseillerClient($client)
{
    $clientInfo = mdlGetClient($client);
    if ($clientInfo == false) {
        vueConseillerMsg("Aucun client n'a été trouvé");
    } else {
        $compte = mdlGetClientCompte($clientInfo->id);
        $contrat = mdlGetContrat($clientInfo->id);
        $allCompte = mdlGetAllTypeAccount();
        $allContrat = mdlGetAllTypeContract();
        vueConseillerClient($clientInfo, $compte, $contrat, $allCompte, $allContrat);
    }
}

function ctrlConseillerClientDeconnection()
{
    vueConseillerClientDeconnection();
}

function ctrlConseillerPageInscriptionClient()
{
    vueConseillerInscriptionClient();
}

function ctrlConseillerInscriptionClient($client)
{
    mdlInscriptionClient($client);
    vueConseillerMsg("Le client a été inscrit");
}

function ctrlConseillerCreationCompte($client){

}

//Erreurs
function ctrlErreur($erreur)
{
    afficherErreur($erreur);
}
