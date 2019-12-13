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

/*
 * System tables
 
$config['tables']['cxz']   				= 'tms_comercial_x_zona';
$config['tables']['axz']   				= 'tms_am_x_zona';
$config['tables']['empresa_tick']		= 'tms_empresa_tick';
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
          k.KundeID AS ClientID, 
          k.Aktiv AS StatusID, 
          k.Vorname AS LastName, 
          k.Nachname AS FirstName, 
          k.IDBetreuer AS ComercialID, 
          k.IDProjektbetreuer AS AMID, 
          k.MandantID AS PermisosID, 
          k.CCEmpfaengerAngebot AS CCQuote,
          k.KontoFIBU AS Comptapro ,
          c.Vorname AS ComercialName, 
          c.Nachname AS ComercialLastName,
          c.eMail,
          a.Vorname AS AMName, 
          a.Nachname AS AMLastName,
          z.Bez AS PermisosName
          FROM      kunde k
          LEFT JOIN mitarbeiter c ON k.IDBetreuer = c.MitarbeiterID
          LEFT JOIN mitarbeiter a ON a.MitarbeiterID = k.IDProjektbetreuer
			    LEFT JOIN x_mandant z   ON k.MandantID = z.MandantID
          WHERE     k.KundeID = $idClient";
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
