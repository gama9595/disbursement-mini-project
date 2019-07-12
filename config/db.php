<?php
class dbconnection
{
    //DATABASE CONFIG
    private $host = "localhost";
    private $db_name = "flip";
    private $username = "root";
    private $password = "";

    public $conn;

    public function cfg()
    {
        $dbconfig['servername'] = $this->host;
        $dbconfig['dbname'] = $this->db_name;
        $dbconfig['user'] = $this->username;
        $dbconfig['pass'] = $this->password;
        
        return $dbconfig;
    }

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
