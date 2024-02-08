<?php
//fichier de configuration
global $servername, $database, $username, $password;
require_once 'config.php';
//fichier de fonctions
require_once 'functions.php';

// Connexion à la base de données
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Définir le mode d'erreur PDO sur exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $message = addUser($conn, $_POST);
        echo $message;
    }

    // Récupérer les utilisateurs depuis la base de données
    $users = getUsers($conn);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100  flex items-center justify-center flex-col">

    <div class="bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
        <h2 class="text-2xl font-semibold mb-4">Inscription</h2>

        <form action="index.php" method="POST" enctype="multipart/form-data">

            <!-- Nom et Prénoms -->

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="firstName" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="firstName" name="firstName" class="mt-1 p-2 w-full border rounded-md">
                </div>
                <div>
                    <label for="lastName" class="block text-sm font-medium text-gray-700">Prénoms</label>
                    <input type="text" id="lastName" name="lastName" class="mt-1 p-2 w-full border rounded-md">
                </div>
            </div>


            <!-- Adresse email -->
            <div class="mt-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Adresse Email</label>
                <input type="email" id="email" name="email" class="mt-1 p-2 w-full border rounded-md">
            </div>

            <!--Date de naissance et Couleur Preférée -->

            <div class="grid grid-cols-2 gap-4 mt-2">
                <div>
                    <label for="birthDate" class="block text-sm font-medium text-gray-700">Date de Naissance</label>
                    <input type="date" id="birthDate" name="birthDate" class="mt-1 p-2 w-full border rounded-md">
                </div>
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700">Couleur Preférée</label>
                    <input type="color" id="color" name="color" class="mt-1 p-5 w-full border rounded-md">
                </div>
            </div>

            <!-- Nationalité et Genre-->
            <div class="grid grid-cols-2 gap-4 mt-2">
                <div>
                    <label for="nationality" class="block text-sm font-medium text-gray-700">Nationalité</label>
                    <select id="nationality" name="nationality" class="mt-1 p-3 w-full border rounded-md"></select>
                </div>
                <div>
                    <label for="genre" class="block text-sm font-medium text-gray-700">Genre</label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" class="form-radio" name="genre" value="masculin">
                        <span class="ml-2">Masculin</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" class="form-radio" name="genre" value="feminin">
                        <span class="ml-2">Féminin</span>
                    </label>
                </div>
            </div>

            <!--  la matière préférée -->
            <div class="mt-2">
                <label class="block text-sm font-medium text-gray-700">Matière(s) préférée(s)</label>
                <div class="mt-2 space-y-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="favoriteSubjects[]" value="anglais">
                        <span class="ml-2">Anglais</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="favoriteSubjects[]" value="francais">
                        <span class="ml-2">Français</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="favoriteSubjects[]" value="espagnol">
                        <span class="ml-2">Espagnol</span>
                    </label>
                </div>
            </div>

            <!-- photo de profil avec aperçu -->
            <div class="mt-2">
                <label for="profilePicture" class="block text-sm font-medium text-gray-700">Photo de profil</label>
                <input type="file" id="profilePicture" name="profilePicture" accept="image/*" class="mt-1 p-2 w-full border rounded-md" onchange="previewImage(event)">
                <div id="imagePreview" class="mt-2"></div>
            </div>

            <!-- Bouton d'enregistrement-->
            <div class="mt-4">
                <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Enregistrer</button>
            </div>
        </form>
    </div>


    <div class="mt-8">
        <!-- component -->
        <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5">
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4 font-medium text-gray-900">Noms et prenoms</th>
                    <th scope="col" class="px-6 py-4 font-medium text-gray-900">Genre</th>
                    <th scope="col" class="px-6 py-4 font-medium text-gray-900">Date de Naissance</th>
                    <th scope="col" class="px-6 py-4 font-medium text-gray-900">Nationalité</th>
                    <th scope="col" class="px-6 py-4 font-medium text-gray-900">Couleur Preférée</th>
                    <th scope="col" class="px-6 py-4 font-medium text-gray-900">Matière preférée</th>
                    <th scope="col" class="px-6 py-4 font-medium text-gray-900"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50">
                    <th class="flex gap-3 px-6 py-4 font-normal text-gray-900">
                        <div class="relative h-10 w-10">
                            <img
                                    class="h-full w-full rounded-full object-cover object-center"
                                    src="<?php echo $user['profilePicture']; ?>"
                                    alt="photo de profil"
                            />
                            <span class="absolute right-0 bottom-0 h-2 w-2 rounded-full bg-green-400 ring ring-white"></span>
                        </div>
                        <div class="text-sm">
                            <div class="font-medium text-gray-700"><?php echo $user['firstName'] . ' ' . $user['lastName']; ?></div>
                            <div class="text-gray-400"><?php echo $user['email']; ?></div>
                        </div>
                    </th>
                    <td class="px-6 py-4">
              <span
                      class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-600"
              >
                <?php echo $user['genre'] ?>
              </span>
                    </td>
                    <td class="px-6 py-4"><?php echo $user['birthDate'] ?></td>
                    <td class="px-6 py-4"><?php echo $user['nationality'] ?></td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <?php
                            $color = $user['color']; // Récupérez la couleur préférée de l'utilisateur
                            $bgClass = 'bg-' . $color . '-500';
                            ?>
                            <span class="inline-flex items-center gap-1 rounded-full <?php echo $bgClass; ?> px-2 py-1 text-xs font-semibold text-black">
                                <?php echo ucfirst($color); ?> <!-- Affichez le nom de la couleur en majuscule -->
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <?php foreach ($user['favoriteSubjects'] as $subject): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600">
                                 <?php echo $subject; ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-4">
                            <a x-data="{ tooltip: 'Delete' }" href="#">
                                <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="h-6 w-6"
                                        x-tooltip="tooltip"
                                >
                                    <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                    />
                                </svg>
                            </a>
                            <a x-data="{ tooltip: 'Edite' }" href="#">
                                <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="h-6 w-6"
                                        x-tooltip="tooltip"
                                >
                                    <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                    />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
<script>
    // API pour récupérer la liste des nationalités
    const nationalitySelect = document.getElementById('nationality');

    // API de nationalités
    const apiUrl = 'https://restcountries.com/v2/all';

    // Effectuer la requête AJAX
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            // Peupler le champ de sélection avec les nationalités
            data.forEach(country => {
                const option = document.createElement('option');
                option.value = country.demonym;
                option.textContent = country.demonym;
                nationalitySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur lors de la récupération des nationalités:', error));
</script>

</html>
