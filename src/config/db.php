<?php

  //Base de datos SISTEMA
  class system_db{
    private $dbSystemHost = 'localhost';
    private $dbSystemName = 'tickibdd';
    private $dbSystemUser = 'root';
    private $dbSystemPass = '';
    // conexion
    public function conectSystemDB(){
      $mysqlConnectSystem = "mysql:host=$this->dbSystemHost;dbname=$this->dbSystemName";
      $dbConexionSystem = new PDO($mysqlConnectSystem, $this->dbSystemUser, $this->dbSystemPass);
      $dbConexionSystem->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbConexionSystem;
    }
  }

  //Base de datos PLUNET
  class db{

    // Old Plunet SERVER 
    /*
    private $dbPlunetHost = '192.168.30.50';
    private $dbPlunetName = 'plunet';
    private $dbPlunetUser = '';
    private $dbPlunetPass = '';
    */

    // New Plunet SERVER 

    private $dbPlunetHost = '10.10.10.132';
    private $dbPlunetName = 'plunet';
    private $dbPlunetUser = '';
    private $dbPlunetPass = '';

    // conexion
    public function conectDB(){
      $mysqlConnectPlunet = "mysql:host=$this->dbPlunetHost;dbname=$this->dbPlunetName";
      $dbConexionPlunet = new PDO($mysqlConnectPlunet, $this->dbPlunetUser, $this->dbPlunetPass);
      $dbConexionPlunet->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbConexionPlunet;
    }
  }

    