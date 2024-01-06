<?php

require_once __DIR__ . '/../Modele/modele.php';
require_once __DIR__ . '/../Vue/vue.php';

//Page login
function ctrlPageLogin(): void
{
    pageLogin();
}

function ctrlVerifierId(): void
{
    $usr = $_POST['login'];
    $mdp = $_POST['mdp'];

    $ligne = verifierLogin($usr, $mdp);

    if (!$ligne) {
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
function ctrlGestion(): void
{
    pageGestion();
}

function ctrlAjouterEmploye(): void
{
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $login = $_POST['login'];
    $mdp = $_POST['mdp'];
    $dateEmbauche = $_POST['dateembauche'];
    $type = $_POST['poste'];

    $ensemble = verifierAvantAjout($nom, $prenom, $login);

    if ($ensemble['personne']) {
        msgGestionEmployes('Personne déjà existante !');
    } elseif ($ensemble['login']) {
        msgGestionEmployes('Login déjà utilisé !');
    } else {
        ajouterEmploye($nom, $prenom, $login, $mdp, $dateEmbauche, $type);
        msgGestionEmployes('Nouvel employé ajouté !');
    }
}

function ctrlModifierEmploye(): void
{
    $login = $_POST['login'];
    $mdp = $_POST['mdp'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];

    $ensemble = verifierAvantAjout($nom, $prenom, $login);

    if (!$ensemble['personne']) {
        msgGestionEmployes('Aucun employé ne correspond à votre saisis.');
    } elseif ($ensemble['login']) {
        msgGestionEmployes('Login déjà utilisé !');
    } else {
        modifierEmploye($login, $mdp, $nom, $prenom);
        msgGestionEmployes('Identifiants changés !');
    }
}

function ctrlGetAllMotif(): void
{
    vueGetAllMotif(mdlGetAllMotif());
}

function ctrlModifierPiece($motif): void
{
    if (isset($motif['modifier'])
        && isset($motif['valeurModifier'])) {

        if (strlen($motif['valeurModifier']) > 0) {

            $id = $motif['modifier'];
            $value = $motif['valeurModifier'];

            mdlModifierPiece($id, $value);

            vueMsgDirecteur('Le motif a bien été modifié');
        } else {
            vueMsgDirecteur('Veuillez remplir le nouveau motif');
        }
    } else {
        vueMsgDirecteur('Veuillez selectionner un motif');
    }
}

//Comptes et contrats
function ctrlGetAllTypeAccountContract(): void
{
    $account = mdlGetAllTypeAccount();
    $contract = mdlGetAllTypeContract();

    vueGetAllTypeAccountContract($account, $contract);
}

function ctrlSupprimerTypeAccount($type): void
{
    if (isset($type['account'])) {
        try {

            $result = mdlTypeIsAssign($type['account'], 'account');

            if (!$result) {

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

            if (!$result) {

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
        vueMsgDirecteur('Pour supprimer un type selectionnez un type');
    }
}

/**
 * @throws Exception
 */
function ctrlAjouterType($newType): void
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

        if (!mdlGetTypeByName($nom, 'account')
            && !mdlGetTypeByName($nom, 'contract')) {

            mdlAjouterType($nature, $nom, $pieceCreation, $pieceModification, $pieceSuppression);

            vueMsgDirecteur('Le '.$nature.'"'.$nom.'" a bien été créer');
        } else {
            vueMsgDirecteur('Le nom "'.$nom.'" est déjà utilisé pour un type de compte ou contrat');
        }
    } else {
        vueMsgDirecteur("Tous les champs de texte n'ont pas été remplis");
    }
}

//Agent -> Modification clients

function ctrlGestionClients(): void
{
    pageGestionClients();
}

function ctrlModifierClient(): void
{
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $ligne = rechercheClient($nom, $prenom);
    if ($ligne) {
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

function ctrlStatistiques(): void
{
    $argent = totalArgent();
    $comptes = nbComptes();
    $contrats = nbContrats();
    $clients = nbClients();
    $employes = nbEmployes();
    vueStatistiques($argent, $comptes, $contrats, $clients, $employes);
}

//Erreurs
function ctrlErreur($erreur): void
{
    afficherErreur($erreur);
}
