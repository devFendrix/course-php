<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', "1024M");
ini_set('max_execution_time', 600);

require './config/database.php';
require './entities/Car.php';

$db = connect();

// Insertion dans la base de données
if(isset($_POST['insert-car'])) {
    Car::create($db ,$_POST['model'], $_POST['brand'], $_POST['price'], $_POST['build_at']);
}
// Suppression d'une entrée dans la base de données
if(isset($_POST['delete-car'])){
    $car = new Car($db, $_POST['id-car']);
    $car->delete();
}

// Mise à jour d'une entrée dans la base de données
if(isset($_POST['update-car'])){
    $car = new Car($db, $_POST['id-car']);
    $car->setModel($_POST['model']);
    $car->setBrand($_POST['brand']);
    $car->setPrice($_POST['price']);
    $car->setBuildAt($_POST['build_at']);
    $car->update();
}

// Récupération des utilisateurs
$query = $db->prepare("SELECT * FROM cars");
$query->execute();
$cars = $query->fetchAll(PDO::FETCH_OBJ);

// Récupération des utilisateurs
$queryUsers = $db->prepare("SELECT * FROM users");
$queryUsers->execute();
$users = $queryUsers->fetchAll(PDO::FETCH_OBJ);

// Ajout d'un utilisateur à un véhicule
if (isset($_POST['add-user-to-car'])) {
    $userId = $_POST['user_id'];
    $carId = $_POST['car_id'];

    try {
        $user = new User($db, $userId); // Vérification de l'utilisateur
        $car = new Car($db, $carId);    // Vérification de la voiture

        $queryAddCar = $db->prepare("INSERT INTO cars_users (id_user, id_car, assigned_at) VALUES (:id_user, :id_car, :assigned_at)");
        $queryAddCar->bindValue(':id_user', $userId, PDO::PARAM_INT);
        $queryAddCar->bindValue(':id_car', $carId, PDO::PARAM_INT);
        $queryAddCar->bindValue(':assigned_at', date("Y-m-d H:i:s"));
        $queryAddCar->execute();

        echo "L'utilisateur a été ajouté à la voiture avec succès.";
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire des voitures </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="button"]:hover {
            background-color: #45a049;
        }

        _table {
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>PHP Test Page</h1>

<?php if ($cars): ?>
    <table>
        <tr>
            <th>Modèle</th>
            <th>Marque</th>
            <th>Prix</th>
            <th>Date de construction</th>
            <th>Action</th>
        </tr>
        <?php foreach ($cars as $car): ?>
            <form class="form-cars" method="POST">
                <tr>
                    <td><input type="text" name="model" value="<?= $car->model ?>"/></td>
                    <td><input type="text" name="brand" value="<?= $car->brand ?>"/></td>
                    <td><input type="text" name="price" value="<?= $car->price ?>"/></td>
                    <td><input type="date" name="build_at" value="<?= $car->build_at ?>"/></td>
                    <td>
                        <input type="hidden" name="id-user" value="<?= $car->id ?>"/>
                        <button class="delete-car" name="delete-car">Supprimer</button>
                        <button class="update-car" name="update-car">Modifier</button>
                    </td>
                </tr>
            </form>
        <?php endforeach;?>
    </table>
<?php endif;?>

<form id="form_user" method="POST">
    <label for="model">Modèle :</label>
    <input type="text" id="model" name="model" placeholder="Modèle..">

    <label for="brand">Marque :</label>
    <input type="text" id="brand" name="brand" placeholder="Marque..">

    <label for="price">Prix :</label>
    <input type="number" id="price" name="price" placeholder="Prix..">

    <label for="build_at">Date de construction :</label>
    <input type="date" id="build_at" name="build_at" placeholder="Date de construction..">

    <input type="submit" name="insert-car" id="submit-btn" value="Envoyer">
</form>



<form method="POST">
    <label for="user_id">Sélectionnez un utilisateur :</label>
    <select name="user_id" required>
        <option value="">-- Choisir un utilisateur --</option>
        <?php foreach ($users as $user): ?>
            <option value="<?= $user->id ?>">
                <?= htmlspecialchars($user->firstname . ' ' . $user->lastname) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="car_id">Sélectionnez une voiture :</label>
    <select name="car_id" required>
        <option value="">-- Choisir une voiture --</option>
        <?php foreach ($cars as $car): ?>
            <option value="<?= $car->id ?>">
                <?= htmlspecialchars($car->brand . ' ' . $car->model) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="submit" name="add-user-to-car" value="Associer utilisateur à la voiture">
</form>
</body>
</html>