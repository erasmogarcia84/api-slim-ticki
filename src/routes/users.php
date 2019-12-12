<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App;

// GET All Clientes
$app->get('/api/test', function(Request $request, Response $response){
  $sql = "SELECT * FROM clientes";
  try{
    $db = new db();
    $db = $db->conectDB();
    $result = $db->query($sql);

    if ($result->rowCount() > 0){
      $clientes = $result->fetchAll(PDO::FETCH_OBJ);
      echo json_encode($clientes);
    } else {
      echo json_encode("No existen clientes en la BBDD");
    }
    $result = null;
    $db = null;
  } catch(PDOException $err) {
    echo '{"error" : {"text":'.$err->getMessage().'}';
  }
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
  $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
  return $handler($req, $res);
});