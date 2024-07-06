<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Configs/Configs.php");


class Database {
    private $pdo;
    private $configs;
    private $dbName;


    public function __construct($configsFileName = "configs.json") {
        

        // Récupération des informations d'identification
        $this->configs = Configs::GetConfigs($configsFileName);
        $this->dbName = $this->configs->dbName;

        $dsn = "mysql:host=".$this->configs->host.";dbname=$this->dbName";
        $username = $this->configs->username;
        $password = $this->configs->password;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $username, $password, $options);
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function create($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':'.implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $stmt;
    }

    public function readAll($table) {
        $stmt = $this->pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll();
    }

    public function read($table, $id, $idName = 'id') {
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE $idName = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function update($table, $id, $idName, $data) {
        $placeholders = '';
        foreach ($data as $key => $value) {
            $placeholders .= $key . ' = :' . $key . ', ';
        }
        $placeholders = rtrim($placeholders, ', ');
        $sql = "UPDATE $table SET $placeholders WHERE $idName = :id";
        $data['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($table, $id, $idName = 'id') {
        $stmt = $this->pdo->prepare("DELETE FROM $table WHERE $idName = :id");
        return $stmt->execute(['id' => $id]);
    }



}
?>
