<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App;

/*
 * Plunet tables ****** PARA REFERENCIA API BALDO
 
$config['tables']['cliente']   	  		= 'kunde';
$config['tables']['comercial']    		= 'mitarbeiter';
$config['tables']['AM']  		 		      = 'mitarbeiter';
$config['tables']['zona'] 		  		  = 'x_mandant';
$config['tables']['fras_cliente']		  = 'rechnungkunde';
$config['tables']['abonos_cliente']  	= 'gutschrift';
$config['tables']['pedidos'] 			    = 'auftrag';
$config['tables']['presupuestos']		  = 'angebot';
$config['tables']['posicion_presu']		= 'angebotposition';
$config['tables']['posicion_pedido'] 	= 'auftragposition';
$config['tables']['fras_traductores']	= 'rechnungmitarbeiter';
$config['tables']['traductor']			  = 'mitarbeiter';
*/

// GET All Clientes
$app->get('/api/tms/clientes', function(Request $request, Response $response){
  //$sql = "SELECT * FROM kunde";
  /* $sql = "SELECT 
  KundeID AS ClientID, 
  Aktiv AS StatusID, 
  Vorname AS LastName, 
  Nachname AS FirstName, 
  IDBetreuer AS ComercialID, 
  c.Vorname AS ComercialName, 
  c.Nachname AS ComercialLastName, 
  c.eMail,
  IDProjektbetreuer AS AMID, 
  a.Vorname AS AMName, 
  a.Nachname AS AMLastName,
  MandantID AS PermisosID, 
  z.Bez AS PermisosName,
  CCEmpfaengerAngebot AS CCQuote,
  KontoFIBU AS Comptapro 
  FROM kunde"; */
  $sql = "SELECT 
          KundeID AS ClientID, 
          Aktiv AS StatusID, 
          Vorname AS LastName, 
          Nachname AS FirstName, 
          IDBetreuer AS ComercialID, 
          IDProjektbetreuer AS AMID, 
          MandantID AS PermisosID, 
          CCEmpfaengerAngebot AS CCQuote,
          KontoFIBU AS Comptapro 
          FROM kunde";
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

// GET All Clientes
$app->get('/api/tms/clientes/{id}', function(Request $request, Response $response){
  $idClient = $request->getAttribute('id');
  $sql = "SELECT 
          KundeID AS ClientID, 
          Aktiv AS StatusID, 
          Vorname AS LastName, 
          Nachname AS FirstName, 
          IDBetreuer AS ComercialID, 
          IDProjektbetreuer AS AMID, 
          MandantID AS PermisosID, 
          CCEmpfaengerAngebot AS CCQuote,
          KontoFIBU AS Comptapro 
          FROM kunde
          WHERE KundeID = $idClient";
  try{
    $db = new db();
    $db = $db->conectDB();
    $result = $db->query($sql);

    if ($result->rowCount() > 0){
      $cliente = $result->fetchAll(PDO::FETCH_OBJ);
      echo json_encode($cliente);
    } else {
      echo json_encode("No existen clientes en la BBDD con este ID");
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