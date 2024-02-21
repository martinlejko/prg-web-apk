<?php


class DatabaseConnection
{
    private $db_config;
    private $mysqli;

    public function __construct()
    {
        require("Database/db_config.php");
        $this->db_config = $db_config;

        $this->mysqli = new mysqli(
            $this->db_config["server"],
            $this->db_config["login"],
            $this->db_config["password"],
            $this->db_config["database"]
        );
    }

    public function getMysqli()
    {
        return $this->mysqli;
    }
}
