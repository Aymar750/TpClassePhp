<?php
// Ajout d'un nouvel utilisateur
function addUser($conn, $data) {
    try {
        // Données du formulaire
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data['email'];
        $birthDate = $data['birthDate'];
        $color = $data['color'];
        $nationality = $data['nationality'];
        $genre = $data['genre'];
        $favoriteSubjects = implode(',', $data['favoriteSubjects']); // Convertir le tableau en chaîne pour la base de données

        // Traitement de la photo de profil
        $profilePicture = ""; // Chemin de la photo de profil par défaut
        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "profils/";
            $targetFile = $targetDir . basename($_FILES["profilePicture"]["name"]);
            if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile)) {
                $profilePicture = $targetFile;
            } else {
                throw new Exception("Erreur lors du téléchargement du fichier.");
            }
        }

        // Requête d'insertion
        $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, birthDate, color, nationality, genre, favoriteSubjects, profilePicture) 
                                VALUES (:firstName, :lastName, :email, :birthDate, :color, :nationality, :genre, :favoriteSubjects, :profilePicture)");

        // Liaison des paramètres avec des valeurs
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':birthDate', $birthDate);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':nationality', $nationality);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':favoriteSubjects', $favoriteSubjects);
        $stmt->bindParam(':profilePicture', $profilePicture);


        $stmt->execute();

        return "Nouvel utilisateur ajouté avec succès.";
    } catch(PDOException $e) {
        return "Erreur lors de l'ajout de l'utilisateur: " . $e->getMessage();
    } catch(Exception $e) {
        return $e->getMessage();
    }
}

// Fonction pour récupérer les utilisateurs depuis la base de données
function getUsers($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque utilisateur, récupérez les matières préférées sous forme de tableau
        foreach ($users as &$user) {
            $user['favoriteSubjects'] = explode(',', $user['favoriteSubjects']);
        }

        return $users;

    } catch(PDOException $e) {
        echo "Erreur lors de la récupération des utilisateurs: " . $e->getMessage();
    }
}

?>
