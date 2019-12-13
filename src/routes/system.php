<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App;

/*
 * System tables
 
$config['tables']['cxz']   				= 'tms_comercial_x_zona';
$config['tables']['axz']   				= 'tms_am_x_zona';
$config['tables']['empresa_tick']		= 'tms_empresa_tick';
*/


// GET All Clientes
$app->get('/api/sistema/am', function(Request $request, Response $response){
  $sql = "SELECT * FROM tms_am_x_zona";
  try{
    $db = new system_db();
    $db = $db->conectSystemDB();
    $result = $db->query($sql);

    if ($result->rowCount() > 0){
      $clientes = $result->fetchAll(PDO::FETCH_OBJ);
      echo json_encode($clientes);
    } else {
      echo json_encode("No existen A.M. en la BBDD");
    }
    $result = null;
    $db = null;
  } catch(PDOException $err) {
    echo '{"error" : {"text":'.$err->getMessage().'}';
  }
});
