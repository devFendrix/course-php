<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', "1024M");
ini_set('max_execution_time', 600);

require './config/database.php';
require './entities/User.php';

$db = connect();

// Insertion dans la base de données
if(isset($_POST['insert-user'])) {
    User::create($db, $_POST['firstname'], $_POST['lastname'], $_POST['username'], $_POST['mail'], $_POST['age'], $_POST['cars']);
}
// Suppression d'une entrée dans la base de données
if(isset($_POST['delete-user'])){
    $user = new User($db, $_POST['id-user']);
    $user->delete();
}

// Mise à jour d'une entrée dans la base de données
if(isset($_POST['update-user'])){
    $user = new User($db, $_POST['id-user']);
    $user->setFirstname($_POST['firstname']);
    $user->setLastname($_POST['lastname']);
    $user->setUsername($_POST['username']);
    $user->setMail($_POST['mail']);
    $user->setAge($_POST['age']);
    $user->update();
}

// Récupération des utilisateurs
$query = $db->prepare("
    SELECT 
        u.firstname, u.lastname, u.username, u.mail, u.age, u.id as user_id,
        c.model, c.brand, c.id   
    FROM users u 
    LEFT JOIN cars_users cu ON u.id = cu.id_user
    LEFT JOIN cars c ON cu.id_car = c.id
    GROUP BY u.id
");
$query->execute();
$users = $query->fetchAll(PDO::FETCH_OBJ);

// Récupération des voitures
$queryCars = $db->prepare("SELECT * FROM cars");
$queryCars->execute();
$cars = $queryCars->fetchAll(PDO::FETCH_OBJ);

//print_r($cars);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire des utilisateurs </title>
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
<a href="exo.php" target="_blank">haha</a>

<?php if ($users): ?>
    <table>
        <tr>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Pseudo</th>
            <th>Mail</th>
            <th>Age</th>
            <th>Voitures</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user):

            $queryHasCars = $db->prepare("
                SELECT c.model, c.brand
                FROM cars_users cu
                JOIN cars c ON cu.id_car = c.id
                WHERE cu.id_user = :id_user
            ");
            $queryHasCars->bindValue('id_user', $user->user_id, PDO::PARAM_INT);
            $queryHasCars->execute();

            $userCars = $queryHasCars->fetchAll(PDO::FETCH_OBJ);
        ?>
            <form class="form-users" method="POST">
                <tr>
                    <td><input type="text" name="firstname" value="<?= $user->firstname ?>"/></td>
                    <td><input type="text" name="lastname" value="<?= $user->lastname ?>"/></td>
                    <td><input type="text" name="username" value="<?= $user->username ?>"/></td>
                    <td><input type="text" name="mail" value="<?= $user->mail ?>"/></td>
                    <td><input type="number" name="age" value="<?= $user->age ?>"/></td>
                    <td>
                        <?php
                            if ($userCars):
                            foreach ($userCars as $userCar):
                        ?>
                            <span><?= $userCar->model . ' / ' . $userCar->brand ?></span><br>
                        <?php
                            endforeach;
                            endif;
                        ?>
                    </td>
                    <td>
                        <input type="hidden" name="id-user" value="<?= $user->user_id ?>"/>
                        <button class="delete-user" name="delete-user">Supprimer</button>
                        <button class="update-user" name="update-user">Modifier</button>
                    </td>
                </tr>
            </form>
        <?php endforeach;?>
    </table>
<?php endif;?>

<form id="form_user" method="POST">
    <label for="firstname">Prénom :</label>
    <input type="text" id="firstname" name="firstname" placeholder="Votre prénom..">

    <label for="lastname">Nom :</label>
    <input type="text" id="lastname" name="lastname" placeholder="Votre nom..">

    <label for="username">Pseudo :</label>
    <input type="text" id="username" name="username" placeholder="Votre pseudo..">

    <label for="mail">Mail :</label>
    <input type="text" id="mail" name="mail" placeholder="Votre adresse mail..">

    <label for="age">Age :</label>
    <input type="number" min="1"  id="age" name="age" placeholder="Votre age..">
    <br>

    <?php if ($cars): ?>
        <label for="cars">Choisissez votre voiture :</label>
        <select name="cars" id="cars">
            <option value="" selected></option>
            <?php foreach ($cars as $car): ?>
                <option value="<?= $car->id ?>"><?= $car->model . ' / ' . $car->brand ?></option>
            <?php endforeach; ?>
        </select>
        <br>
    <?php endif; ?>


    <input type="submit" name="insert-user" id="submit-btn" value="Envoyer">
</form>

</body>
</html>