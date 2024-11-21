<?php

class Car {
    private int $id;
    private string $model;
    private string $brand;
    private float $price;
    private string $build_at;
    private PDO $db;
    public function __construct($db, $id)
    {
        $query = $db->prepare("SELECT * FROM cars WHERE id = :id");
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute();

        $car = $query->fetchObject();

        if(!$car) {
            throw new InvalidArgumentException("Pas de car avec cet id");
        }

        $this->id = $id;
        $this->model = $car->model;
        $this->brand = $car->brand;
        $this->price = $car->price;
        $this->build_at = $car->build_at;
        $this->db = $db;
    }

    public static function create($db, $model, $brand, $price, $build_at): Car
    {
        $query = $db->prepare("INSERT INTO cars (model, brand, price, build_at) VALUES (:model, :brand, :price, :build_at)");
        $query->bindValue(':model', $model);
        $query->bindValue(':brand', $brand);
        $query->bindValue(':price', $price, PDO::PARAM_STR);
        $query->bindValue(':build_at', $build_at, PDO::PARAM_STR);
        $query->execute();

        return new Car($db, $db->lastInsertId());
    }

    public function delete(): void
    {
        $query = $this->db->prepare("DELETE FROM cars WHERE id = :id");
        $query->bindValue(":id", $this->id, PDO::PARAM_INT);
        $query->execute();
    }

    public function update(): void
    {
        $query = $this->db->prepare("UPDATE cars SET model = :model, brand = :brand, price = :price WHERE id = :id");
        $query->bindValue(':model', $this->model);
        $query->bindValue(':brand', $this->brand);
        $query->bindValue(':price', $this->price);
        $query->bindValue(':build_at', $this->build_at);
        $query->execute();
        $query->bindValue(":id", $this->id);
        $query->execute();

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getBuildAt(): string
    {
        return $this->build_at;
    }

    public function setBuildAt(string $build_at): void
    {
        $this->build_at = $build_at;
    }
    // getter/setter

}