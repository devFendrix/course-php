<?php
/**
 * Class User
 *
 * Un objet : Une instanciation d'une class
 * Une class est constitué de :
 *  - Propriétés ou attributs -> définir un état
 *  - Méthodes -> définir un comportement
 */

class User {
    private int $id;
    private string $firstname;
    private string $lastname;
    private string $username;
    private string $mail;
    private int $age;
    private PDO $db;
    public function __construct($db, $id)
    {
        $query = $db->prepare("SELECT * FROM users WHERE id = :id");
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute();

        $user = $query->fetchObject();

        if(!$user) {
            throw new InvalidArgumentException("Pas d'user avec cet id");
        }

        $this->id = $id;
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->username = $user->username;
        $this->mail = $user->mail;
        $this->age = $user->age;
        $this->db = $db;
    }

    public static function create($db, $firstname, $lastname, $username, $mail, $age, $carId): User
    {
        $query = $db->prepare("INSERT INTO users (firstname, lastname, username, mail, age) VALUES (:firstname, :lastname, :username, :mail, :age)");
        $query->bindValue(':firstname', $firstname);
        $query->bindValue(':lastname', $lastname);
        $query->bindValue(':username', $username);
        $query->bindValue(':mail', $mail);
        $query->bindValue(':age', $age, PDO::PARAM_INT);
        $query->execute();

        $userId = $db->lastInsertId();

        if($carId) {
            $queryAddCar = $db->prepare("INSERT INTO cars_users (id_user, id_car, assigned_at) VALUES (:id_user, :id_car, :assigned_at)");
            $queryAddCar->bindValue(':id_user', $userId, PDO::PARAM_INT);
            $queryAddCar->bindValue(':id_car', $carId, PDO::PARAM_INT);
            $queryAddCar->bindValue(':assigned_at', date("Y-m-d H:i:s"));
            $queryAddCar->execute();
        }

        return new User($db, $userId);
    }

    public function delete(): void
    {
        $query = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $query->bindValue(":id", $this->id, PDO::PARAM_INT);
        $query->execute();
    }

    public function update(): void
    {
        $query = $this->db->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, username = :username, mail = :mail, age = :age  WHERE id = :id");
        $query->bindValue(":firstname", $this->firstname);
        $query->bindValue(":lastname", $this->lastname);
        $query->bindValue(":username", $this->username);
        $query->bindValue(":mail", $this->mail);
        $query->bindValue(":age", $this->age, PDO::PARAM_INT);
        $query->bindValue(":id", $this->id);
        $query->execute();

    }
    // getter/setter
    public function getId(): int
    {
        return $this->id;
    }
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }
    public function getLastname(): string
    {
        return $this->lastname;
    }
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }
}