<?php
    require 'connection.php';

    /* Exécute une requête préparée et renvoie le statement (SELECT/INSERT/UPDATE/DELETE) */
    function db_exec($sql, $types = '', ...$params) {
        global $mysqli;
        $stmt = mysqli_prepare($mysqli, $sql);
        if ($types !== '') {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        return $stmt;
    }

    function get_all_utilisateur(){
        global $mysqli;
        $query = "SELECT * FROM utilisateur";
        $result = mysqli_query($mysqli, $query);
        $utilisateur = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $utilisateur;
    }

    /* connexion - inscription */

    function insert_utilisateur($identifiant, $mdp) {
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);

        // Vérifier si identifiant existe
        $stmt = db_exec("SELECT idUser FROM utilisateur WHERE identifiant = ? LIMIT 1", 's', $identifiant);
        $resultCheck = mysqli_stmt_get_result($stmt);
        if (mysqli_fetch_assoc($resultCheck)) {
            return false; // L'identifiant existe déjà
        }
        // Insertion
        $stmt = db_exec("INSERT INTO utilisateur (identifiant, mdp) VALUES (?, ?)", 'ss', $identifiant, $mdp);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }

    function connecte_utilisateur($identifiant, $mdp) {
        $stmt = db_exec("SELECT * FROM utilisateur WHERE identifiant = ? LIMIT 1", 's', $identifiant);
        $result = mysqli_stmt_get_result($stmt);
        $utilisateur = mysqli_fetch_assoc($result);
        if (!$utilisateur) { // Utilisateur introuvable
            return false;
        }
        if (!password_verify($mdp, $utilisateur['mdp'])) { // Vérification du mot de passe (haché avec password_hash)
            return false;
        }
        $_SESSION['utilisateur'] = $utilisateur;// Connexion : on enregistre dans la session
        return true;
    }


    /* acceuil + categorie */

    function get_all_categorie(){
        global $mysqli;
        $query = "SELECT * FROM categorie";
        $result = mysqli_query($mysqli, $query);
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $categories;
    }

    function get_categorie($idCat) {
        $stmt = db_exec("SELECT * FROM categorie WHERE idCat = ?", 'i', $idCat);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    function get_messages_par_categorie($idCat, $userId){
        $query = "
            SELECT message.*, utilisateur.identifiant AS auteur, reaction.IdType AS userReactionType
            FROM message
            JOIN utilisateur ON message.IdUser = utilisateur.IdUser
            LEFT JOIN reaction ON reaction.IdMsg = message.idMsg AND reaction.IdUser = ?
            WHERE message.IdCat = ?
            ORDER BY message.date DESC
        ";
        $stmt = db_exec($query, 'ii', $userId, $idCat);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    function get_messages_par_categorie_trier_par_date($idCat, $userId){
        $query = "
            SELECT message.*, utilisateur.identifiant AS auteur, reaction.IdType AS userReactionType
            FROM message
            JOIN utilisateur ON message.IdUser = utilisateur.IdUser
            LEFT JOIN reaction ON reaction.IdMsg = message.idMsg AND reaction.IdUser = ?
            WHERE message.IdCat = ?
            ORDER BY message.date DESC
        ";
        $stmt = db_exec($query, 'ii', $userId, $idCat);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    function get_messages_par_categorie_trier_par_like($idCat, $userId){
        /* Récupère les messages d'une catégorie triés par nombre de likes */
        $query = "
            SELECT message.*, utilisateur.identifiant AS auteur, reaction.IdType AS userReactionType
            FROM message
            JOIN utilisateur ON message.IdUser = utilisateur.IdUser
            LEFT JOIN reaction ON reaction.IdMsg = message.idMsg AND reaction.IdUser = ?
            WHERE message.IdCat = ?
            ORDER BY message.nbrLike DESC
        ";
        $stmt = db_exec($query, 'ii', $userId, $idCat);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    function get_messages_par_utilisateur($idUser){
        /* Récupère les messages d'un utilisateur */
        $query = "
            SELECT message.*, categorie.nom AS categorieNom
            FROM message
            JOIN categorie ON message.IdCat = categorie.idCat
            WHERE message.IdUser = ?
            ORDER BY message.date DESC
        ";
        $stmt = db_exec($query, 'i', $idUser);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    function get_messages_par_id($messageId, $userId){
        /* Récupère un message par son ID */
        $query = "
            SELECT message.*, utilisateur.identifiant AS auteur, reaction.IdType AS userReactionType
            FROM message
            JOIN utilisateur ON message.IdUser = utilisateur.IdUser
            LEFT JOIN reaction ON reaction.IdMsg = message.idMsg AND reaction.IdUser = ?
            WHERE message.idMsg = ?
        ";
        $stmt = db_exec($query, 'ii', $userId, $messageId);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }


    /* publier */

    /* Extensions et taille autorisées pour les images jointes à un message */
    const IMAGE_EXTENSIONS_AUTORISEES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const IMAGE_TAILLE_MAX_OCTETS = 5 * 1024 * 1024; // 5 Mo

    function insert_message_avec_image($idCat, $idUser, $texte) {
        // Dossier des uploads
        $upload_dir = "images-upload/";
        // Création du dossier si absent
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $imageSrc = "";
        // Si une image est envoyée
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            // Nettoyage du nom du fichier
            $originalName = basename($_FILES['image']['name']);
            $originalName = str_replace(" ", "-", $originalName); // remplace les espaces
            $originalName = strtolower($originalName);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $extensionValide = in_array($extension, IMAGE_EXTENSIONS_AUTORISEES, true);
            $tailleValide = $_FILES['image']['size'] <= IMAGE_TAILLE_MAX_OCTETS;
            $estUneVraieImage = $extensionValide && $tailleValide && getimagesize($_FILES['image']['tmp_name']) !== false;

            if ($estUneVraieImage) {
                // Création du nom final
                $timestamp = time();
                $newName = "$timestamp-$originalName";
                // Chemin complet pour sauvegarde
                $imagePath = $upload_dir . $newName;
                // Upload du fichier
                if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                    $imageSrc = $imagePath; // ce qui sera enregistré en BDD
                }
            }
        }
        $query = "INSERT INTO message (date, texte, imageSrc, nbrLike, nbrDislike, nbrCom, IdCat, IdUser)
            VALUES (NOW(), ?, ?, 0, 0, 0, ?, ?)";
        $stmt = db_exec($query, 'ssii', $texte, $imageSrc, $idCat, $idUser);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }

    /* Supprimer message */

    function delete_message($messageId){
        /* Supprime un message par son ID */
        db_exec("DELETE FROM reaction WHERE IdMsg = ?", 'i', $messageId);
        /* Supprime les commentaires associés */
        db_exec("DELETE FROM commentaire WHERE IdMsg = ?", 'i', $messageId);
        /* Supprime le message */
        $stmt = db_exec("DELETE FROM message WHERE idMsg = ?", 'i', $messageId);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }

    function delete_utilisateur($idUser){
        global $mysqli;
        $stmt = db_exec("SELECT idMsg FROM message WHERE IdUser = ?", 'i', $idUser);
        $resultMessages = mysqli_stmt_get_result($stmt);
        while ($message = mysqli_fetch_assoc($resultMessages)) {
            delete_message($message['idMsg']);
        }
        db_exec("DELETE FROM commentaire WHERE IdUser = ?", 'i', $idUser);
        db_exec("DELETE FROM reaction WHERE IdUser = ?", 'i', $idUser);
        $stmt = db_exec("DELETE FROM utilisateur WHERE IdUser = ?", 'i', $idUser);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }

    function delete_commentaire($idCom){
        // Récupère l'IdMsg du commentaire pour mettre à jour le nombre de commentaires
        $stmt = db_exec("SELECT IdMsg FROM commentaire WHERE IdComment = ?", 'i', $idCom);
        $commentaire = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$commentaire) {
            return false; // le commentaire n'existe pas (ou déjà supprimé)
        }
        $idMsg = $commentaire['IdMsg'];

        $stmt = db_exec("DELETE FROM commentaire WHERE IdComment = ?", 'i', $idCom);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Met à jour le nombre de commentaires dans la table message
            db_exec("UPDATE message SET nbrCom = nbrCom - 1 WHERE idMsg = ?", 'i', $idMsg);
            return true; // succès
        } else {
            return false; // échec
        }
    }


    /* paramètre */

    function update_biographie($idUser, $biographie){
        /*  Met à jour la biographie d'un utilisateur */
        $stmt = db_exec("UPDATE utilisateur SET biographie = ? WHERE IdUser = ?", 'si', $biographie, $idUser);
        return $stmt !== false;
    }

    function update_identifiant($idUser, $identifiant){
        /*  Met à jour l'identifiant d'un utilisateur */
        $stmt = db_exec("SELECT idUser FROM utilisateur WHERE identifiant = ? LIMIT 1", 's', $identifiant);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
            return false; // L'identifiant existe déjà
        }
        /* Met à jour l'identifiant */
        $stmt = db_exec("UPDATE utilisateur SET identifiant = ? WHERE IdUser = ?", 'si', $identifiant, $idUser);
        return $stmt !== false;
    }

    function reload_session_user($idUser){
        /* Recharge les données de l'utilisateur dans la session */
        $stmt = db_exec("SELECT * FROM utilisateur WHERE IdUser = ? LIMIT 1", 'i', $idUser);
        $utilisateur = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if ($utilisateur) {
            // Recharge les données de session
            $_SESSION['utilisateur'] = $utilisateur;
            return true;
        }
        return false;
    }

    // réaction


    function get_all_commentaire_par_message($messageId){
        /* Récupère tous les commentaires d'un message */
        $query = "SELECT *,utilisateur.identifiant AS auteur
        FROM commentaire INNER JOIN utilisateur
        ON commentaire.IdUser = utilisateur.IdUser WHERE IdMsg = ?
        ORDER BY dateCom ASC";
        $stmt = db_exec($query, 'i', $messageId);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    function get_all_commentaire(){
        global $mysqli;
        $query = "SELECT *,utilisateur.identifiant AS auteur
        FROM commentaire INNER JOIN utilisateur
        ON commentaire.IdUser = utilisateur.IdUser
        ORDER BY dateCom DESC";
        $result = mysqli_query($mysqli, $query);
        $commentaire = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $commentaire;
    }

    function get_all_reaction(){
        global $mysqli;
        /* Récupère toutes les réactions */
        $query = "SELECT * FROM reaction";
        $result = mysqli_query($mysqli, $query);
        $reaction = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $reaction;
    }

    function insert_commentaire($idMsg, $idUser, $texte) {
        /* Insère un commentaire */
        $query = "INSERT INTO commentaire (texte, dateCom, IdMsg, IdUser)
            VALUES (?, NOW(), ?, ?)";
        $stmt = db_exec($query, 'sii', $texte, $idMsg, $idUser);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Met à jour le nombre de commentaires dans la table message
            db_exec("UPDATE message SET nbrCom = nbrCom + 1 WHERE idMsg = ?", 'i', $idMsg);
            return true; // succès
        } else {
            return false; // échec
        }
    }

    function insert_reaction_like($messageId, $userId){
        // Vérifie si l'utilisateur a déjà liké le message
        $stmt = db_exec("SELECT * FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 1 LIMIT 1", 'ii', $messageId, $userId);
        // Si oui, on supprime le like
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
            db_exec("DELETE FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 1", 'ii', $messageId, $userId);
            db_exec("UPDATE message SET nbrLike = nbrLike - 1 WHERE idMsg = ?", 'i', $messageId);
            return true; // succès
        }
        // Vérifie si l'utilisateur a déjà disliké le message
        $stmt = db_exec("SELECT * FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 2 LIMIT 1", 'ii', $messageId, $userId);
        // Si oui, on supprime le dislike
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
            db_exec("DELETE FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 2", 'ii', $messageId, $userId);
            db_exec("UPDATE message SET nbrDislike = nbrDislike - 1 WHERE idMsg = ?", 'i', $messageId);
        }
        // Insère le like
        $stmt = db_exec("INSERT INTO reaction (IdMsg, IdUser, IdType) VALUES (?, ?, 1)", 'ii', $messageId, $userId);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Met à jour le nombre de likes dans la table message
            db_exec("UPDATE message SET nbrLike = nbrLike + 1 WHERE idMsg = ?", 'i', $messageId);
            return true; // succès
        } else {
            return false; // échec
        }
    }

    function insert_reaction_dislike($messageId, $userId){
        // Vérifie si l'utilisateur a déjà disliké le message
        $stmt = db_exec("SELECT * FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 2 LIMIT 1", 'ii', $messageId, $userId);
        // Si oui, on supprime le dislike
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
            db_exec("DELETE FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 2", 'ii', $messageId, $userId);
            db_exec("UPDATE message SET nbrDislike = nbrDislike - 1 WHERE idMsg = ?", 'i', $messageId);
            return true; // succès
        }
        // Vérifie si l'utilisateur a déjà liké le message
        $stmt = db_exec("SELECT * FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 1 LIMIT 1", 'ii', $messageId, $userId);
        // Si oui, on supprime le like
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
            db_exec("DELETE FROM reaction WHERE IdMsg = ? AND IdUser = ? AND IdType = 1", 'ii', $messageId, $userId);
            db_exec("UPDATE message SET nbrLike = nbrLike - 1 WHERE idMsg = ?", 'i', $messageId);
        }
        // Insère le dislike
        $stmt = db_exec("INSERT INTO reaction (IdMsg, IdUser, IdType) VALUES (?, ?, 2)", 'ii', $messageId, $userId);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Met à jour le nombre de dislikes dans la table message
            db_exec("UPDATE message SET nbrDislike = nbrDislike + 1 WHERE idMsg = ?", 'i', $messageId);
            return true; // succès
        } else {
            return false; // échec
        }
    }
?>
