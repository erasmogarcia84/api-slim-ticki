<?php
/*
  //Base de datos LOCAL
  class db{
    private $dbHost = 'localhost';
    private $dbName = 'api_slim';
    private $dbUser = 'tickadmin';
    private $dbPass = 'password';
    // conexion
    public function conectDB(){
      $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
      $dbConexion = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
      $dbConexion->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbConexion;
    }
  }
*/
  //Base de datos PLUNET
  class db{
    private $dbHost = '192.168.30.50';
    private $dbName = 'plunet';
    private $dbUser = 'TickIrd';
    private $dbPass = 'XG0rZvTVU9';
    // conexion
    public function conectDB(){
      $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
      $dbConexion = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
      $dbConexion->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbConexion;
    }
  }
  