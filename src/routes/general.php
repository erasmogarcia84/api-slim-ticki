<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App;


/* **************
     TICKI API
   ************** */


// GET Todos los AM
$app->get('/api/sistema/am', function(Request $request, Response $response){
  $sqlSystem = "SELECT * FROM tms_am_x_zona";
  try{
    $db = new system_db();
    $db = $db->conectSystemDB();
    $result = $db->query($sqlSystem);

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
  return $response;
});


// POST Crear nuevo AM
$app->post('/api/sistema/am/add', function(Request $request, Response $response) {
  $amID = $request->getParam('amID');
  $amName = $request->getParam('amName');
  $zonaID = $request->getParam('zonaID');
  $clientID = $request->getParam('clientID');

  $sqlSystem = "INSERT INTO tms_am_x_zona (amID, amName, zonaID, clientID) 
                VALUES (:amID, :amName, :zonaID, :clientID)";
  try {
    $db = new db();
    $db = $db->conectSystemDB();
    $result = $db->prepare($sqlSystem);

    $result->bindParam(':amID', $amID);
    $result->bindParam(':amName', $amName);
    $result->bindParam(':zonaID', $zonaID);
    $result->bindParam(':clientID', $clientID);

    $result->execute();
    echo json_encode("Nuevo A.M. registrado");
   
    $result = null;
    $db = null;
  } catch(PDOException $e){
    echo '{"error" : {"text":'.$e->getMessage().'}';
  }
});


// GET Todos los Comerciales
$app->get('/api/sistema/comercial', function(Request $request, Response $response){
  $sqlSystem = "SELECT * FROM tms_comercial_x_zona";
  try{
    $db = new system_db();
    $db = $db->conectSystemDB();
    $result = $db->query($sqlSystem);

    if ($result->rowCount() > 0){
      $clientes = $result->fetchAll(PDO::FETCH_OBJ);
      echo json_encode($clientes);
    } else {
      echo json_encode("No existen Comerciales en la BBDD");
    }
    $result = null;
    $db = null;
  } catch(PDOException $err) {
    echo '{"error" : {"text":'.$err->getMessage().'}';
  }
  return $response;
});


/* **************
     PLUNET API
   ************** */


// GET All Clientes
$app->get('/api/tms/clientes', function(Request $request, Response $response){
  $sqlPlunet = "SELECT 
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
    $result = $db->query($sqlPlunet);

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


// GET Cliente Detalles ID
$app->get('/api/tms/clientes/{id}', function(Request $request, Response $response){
  $idClient = $request->getAttribute('id');
  $sqlPlunet = "SELECT 
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
    $result = $db->query($sqlPlunet);

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


// GET Facturas Pendientes
$app->get('/api/tms/facturas/pendientes', function(Request $request, Response $response){
  $sqlPlunet = "SELECT
      r.AnzeigeName AS 'Factura',
      r.DatumRechnung AS 'DataFactura',
      CASE
      WHEN !(a.Bundesland IS NULL or a.Bundesland = '') THEN a.Bundesland
      WHEN !(a.Ort IS NULL or a.Ort = '') THEN a.Ort
      ELSE k.KundeID
      END AS 'Localitat',
      CONCAT(k.Nachname,' ', k.Vorname) AS 'Client',
      CASE
      WHEN r.`Status` = 1 then 'Pendiente'
      WHEN r.`Status` = 2 then 'Pagado'
      WHEN r.`Status` = 3 then 'Cancelado'
      WHEN r.`Status` = 4 then 'Deuda reclamada'
      WHEN r.`Status` = 5 then 'En preparación'
      WHEN r.`Status` = 6 then 'Incobrable'
      WHEN r.`Status` = 7 then 'Abono/Cancelar factura'
      ELSE 'Desconocido'
      END AS 'Estat',
      r.preis AS 'PreuTotal',
      GREATEST(r.DatumZahlbarBis, IFNULL(v.Inhalt_Datum, \"0000-00-00 00.00:00\")) AS 'DataVenciment',
      w.`Text` AS 'FormaPagament',
      k.Bankname AS 'Banc',
      k.IBAN AS 'IBAN'
    FROM rechnungkunde r
    LEFT JOIN kunde k ON r.IDKunde = k.kundeID
    LEFT JOIN x_besteuerungsart x ON x.BesteuerungsArtID = r.IDBesteuerungsArt
    LEFT JOIN x_erloeskonten e ON e.KontoID = r.ErloesKontoID
    LEFT JOIN ausgangsrechnung_textmodul m ON m.IDTextModul = 17 AND r.RechnungKundeID = m.IDMain
    LEFT JOIN Adresse a ON a.AdresseID = r.AnschriftID
    LEFT JOIN w_x_zahlungsmethode_zusatzsprache w ON w.DokSpracheID = 2 /* Español */ AND w.ZahlungsmethodeID = k.ZahlungsMethode
    LEFT JOIN ausgangsrechnung_textmodul v ON v.IDTextModul = 31 AND r.RechnungKundeID = v.IDMain
    WHERE r.`Status` = 1 OR r.`Status` = 4
    UNION
    SELECT
    REPLACE(g.AnzeigeGutschriftNummer, 'CN', 'R-') AS 'Factura',
    g.DatumGutschrift AS 'DataFactura',
    CASE
      WHEN !(a.Bundesland IS NULL or a.Bundesland = '') THEN a.Bundesland
      WHEN !(a.Ort IS NULL or a.Ort = '') THEN a.Ort
      ELSE k.KundeID
    END AS 'Localitat',
    CASE
      WHEN k.Anrede = 3 THEN k.Nachname
      ELSE CONCAT(k.Vorname, ' ', k.Nachname)
    END AS 'Client',
    CASE
      WHEN g.`Status` = 1 then 'En preparación'
      WHEN g.`Status` = 2 then 'Pendiente de pago'
      WHEN g.`Status` = 3 then 'Liquidado'
      WHEN g.`Status` = 4 then 'Cancelado'
      WHEN g.`Status` = 5 then 'Abono/Cancelar factura'
      WHEN g.`Status` = 6 then 'Pagado'
    ELSE 'Desconocido'
    END AS 'Estat',
    (g.Brutto * -1) AS 'Preu Total',
    g.DatumGutschrift AS 'DataVenciment',
    w.`Text` AS 'FormaPagament',
    k.Bankname AS 'Banc',
    k.IBAN AS 'IBAN'
    FROM gutschrift g
    LEFT JOIN Kunde k ON k.KundeID = g.KundeID
    LEFT JOIN Adresse a ON a.AdresseID = g.AnschriftID
    LEFT JOIN x_besteuerungsart x ON x.BesteuerungsArtID = g.IDBesteuerungsArt
    LEFT JOIN x_erloeskonten e ON e.KontoID = g.ErloesKontoID
    LEFT JOIN gutschrift_textmodul m ON m.IDTextModul = 17 AND g.GutschriftID = m.IDMain
    LEFT JOIN w_x_zahlungsmethode_zusatzsprache w ON w.DokSpracheID = 2 /* Español */ AND w.ZahlungsmethodeID = k.ZahlungsMethode
    WHERE g.`Status` = 1
    ORDER BY `Factura`";
  try{
    $db = new db();
    $db = $db->conectDB();
    $result = $db->query($sqlPlunet);

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


// GET Facturas Reclamacion
$app->get('/api/tms/facturas/pagos/{year}', function(Request $request, Response $response){
  $year = $request->getAttribute('year');
  $sqlPlunet = "SELECT
  REPLACE(g.AnzeigeGutschriftNummer, 'CN', 'R-') AS 'NumeroFactura',
  g.DatumGutschrift AS 'Data',
  CASE
    WHEN !(a.Bundesland IS NULL or a.Bundesland = '') THEN a.Bundesland
    WHEN !(a.Ort IS NULL or a.Ort = '') THEN a.Ort
    ELSE k.KundeID
  END AS 'Localitat',
  l.`Text` AS 'Pais',
  CASE
    WHEN k.Anrede = 3 THEN k.Nachname
    ELSE CONCAT(k.Vorname, ' ', k.Nachname)
  END AS 'Client',
  'MIRAR FACTURA' AS 'Comercial',
  CASE
    WHEN t2.`Inhalt` = 63 then 'BIG'
    WHEN t2.`Inhalt` = 64 then 'LOYAL'
    WHEN t2.`Inhalt` = 65 then 'POTENTIAL'
    WHEN t2.`Inhalt` = 66 then 'INDIVIDUAL'
    WHEN t2.`Inhalt` = 67 then 'PREMIUM'
    WHEN t2.`Inhalt` = 68 then 'VIP'
  ELSE 'Desconocido'
  END AS 'CategoriaClient',
  CASE
    WHEN g.`Status` = 1 then 'En preparación'
    WHEN g.`Status` = 2 then 'Pendiente de pago'
    WHEN g.`Status` = 3 then 'Liquidado'
    WHEN g.`Status` = 4 then 'Cancelado'
    WHEN g.`Status` = 5 then 'Abono/Cancelar factura'
    WHEN g.`Status` = 6 then 'Pagado'
  ELSE 'Desconocido'
  END AS 'Estat',
  CASE
    WHEN g.`AuftragArtID` = 1 then 'Traducció'
    WHEN g.`AuftragArtID` = 2 then 'Interpretació'
    WHEN g.`AuftragArtID` = 3 then 'Cultural'
  ELSE g.`AuftragArtID`
  END AS 'Servei',
  (g.Netto * -1) AS 'Base',
  (g.MWStBrutto1+g.MWStBrutto2+g.MWStBrutto3+g.MWStBrutto4+g.MWStBrutto5 * -1) AS 'IVA',
  (g.Brutto * -1) AS 'Total',
  CASE
    WHEN g.Status = 5 OR g.Status = 6 THEN (g.Brutto * -1)
    ELSE ''
  END AS 'Cobrat',
  CASE
    WHEN t1.Inhalt = 52 THEN 'BBVA'
    WHEN t1.Inhalt = 53 THEN 'BANC SABADELL'
    WHEN t1.Inhalt = 54 THEN 'LA CAIXA'
    WHEN t1.Inhalt = 73 THEN 'BANC POPULAR'
    WHEN t1.Inhalt = 104 THEN 'BANKINTER'
    WHEN t1.Inhalt = 131 THEN 'BBVA GROUP'
    ELSE t1.Inhalt
  END AS 'Banc',
  g.DatumGutschrift AS 'DataVenciment',
  NULL AS 'VencimentConfirmat',
  g.DatumGutschrift AS 'DataPagat',
  CASE
    WHEN t1.Inhalt = 52 AND g.Status = 6 THEN g.Brutto /* 'BBVA' */
    ELSE ''
  END AS 'BBVA',
  CASE
    WHEN t1.Inhalt = 53 AND g.Status = 6 THEN g.Brutto /* 'Banc Sabadell' */
    ELSE ''
  END AS 'BANC SABADELL',
  CASE
    WHEN t1.Inhalt = 54 AND g.Status = 6 THEN g.Brutto /* 'LKXA' */
    ELSE ''
  END AS 'LA CAIXA',
  CASE
    WHEN t1.Inhalt = 73 AND g.Status = 6 THEN g.Brutto /* 'Banc Popular' */
    ELSE ''
  END AS 'BANC POPULAR',
  CASE
    WHEN t1.Inhalt = 104 AND g.Status = 6 THEN g.Brutto /* 'Bankinter' */
    ELSE ''
  END AS 'BANKINTER',
  CASE
    WHEN t1.Inhalt = 131 AND g.Status = 6 THEN g.Brutto /* 'BBVA GROUP' */
    ELSE ''
  END AS 'BBVA GROUP'			
FROM gutschrift g
LEFT JOIN Kunde k ON k.KundeID = g.KundeID
LEFT JOIN Adresse a ON a.AdresseID = g.AnschriftID
LEFT JOIN kunde_textmodul t1 ON t1.IDTextModul = 19 AND t1.IDMain = g.KundeID
LEFT JOIN kunde_textmodul t2 ON t2.IDTextModul = 22 AND t2.IDMain = g.KundeID
LEFT JOIN w_land_zusatzsprache l ON l.LandID = a.IDLand AND DokSpracheID = 2 /* DokSpracheID = 2 en Español */
WHERE YEAR(g.DatumGutschrift) = $year
UNION
SELECT
  r.AnzeigeName AS 'NumeroFactura',
  r.DatumRechnung AS 'Data',
  CASE
    WHEN !(a.Bundesland IS NULL or a.Bundesland = '') THEN a.Bundesland
    WHEN !(a.Ort IS NULL or a.Ort = '') THEN a.Ort
    ELSE k.KundeID
  END AS 'Localitat',
  l.`Text` AS 'Pais',
  CASE
    WHEN k.Anrede = 3 THEN k.Nachname
    ELSE CONCAT(k.Vorname, ' ', k.Nachname)
  END AS 'Client',
  CONCAT(m.Vorname, ' ', m.Nachname) AS 'Comercial',
  CASE
    WHEN t2.`Inhalt` = 63 then 'BIG'
    WHEN t2.`Inhalt` = 64 then 'LOYAL'
    WHEN t2.`Inhalt` = 65 then 'POTENTIAL'
    WHEN t2.`Inhalt` = 66 then 'INDIVIDUAL'
    WHEN t2.`Inhalt` = 67 then 'PREMIUM'
    WHEN t2.`Inhalt` = 68 then 'VIP'
  ELSE 'Desconocido'
  END AS 'CategoriaClient',
  CASE
    WHEN r.`Status` = 1 then 'Pendiente'
    WHEN r.`Status` = 2 then 'Pagado'
    WHEN r.`Status` = 3 then 'Cancelado'
    WHEN r.`Status` = 4 then 'Deuda reclamada'
    WHEN r.`Status` = 5 then 'En preparación'
    WHEN r.`Status` = 6 then 'Incobrable'
    WHEN r.`Status` = 7 then 'Abono/Cancelar factura'
  ELSE 'Desconocido'
  END AS 'Estat',
  CASE
    WHEN r.`IDAuftragArt` = 1 then 'Traducció'
    WHEN r.`IDAuftragArt` = 2 then 'Interpretació'
    WHEN r.`IDAuftragArt` = 3 then 'Cultural'
  ELSE r.`IDAuftragArt`
  END AS 'Servei',
  CASE
    WHEN (r.MWStBrutto1+r.MWStBrutto2+r.MWStBrutto3+r.MWStBrutto4+r.MWStBrutto5) = 0 then r.preis
    ELSE (r.MWStBrutto1+r.MWStBrutto2+r.MWStBrutto3+r.MWStBrutto4+r.MWStBrutto5)
  END AS 'Base',
  CASE
    WHEN (r.MWStBrutto1+r.MWStBrutto2+r.MWStBrutto3+r.MWStBrutto4+r.MWStBrutto5) = 0 then 0
    ELSE (r.preis - (r.MWStBrutto1+r.MWStBrutto2+r.MWStBrutto3+r.MWStBrutto4+r.MWStBrutto5))
  END AS 'IVA',
  r.preis AS 'Total',
  CASE
    WHEN r.Status = 2 THEN r.preis
    ELSE ''
  END AS 'Cobrat',
  CASE
    WHEN t1.Inhalt = 52 THEN 'BBVA'
    WHEN t1.Inhalt = 53 THEN 'BANC SABADELL'
    WHEN t1.Inhalt = 54 THEN 'LA CAIXA'
    WHEN t1.Inhalt = 73 THEN 'BANC POPULAR'
    WHEN t1.Inhalt = 104 THEN 'BANKINTER'
    WHEN t1.Inhalt = 131 THEN 'BBVA GROUP'
    ELSE t1.Inhalt
  END AS 'Banc',
  r.DatumZahlbarBis AS 'DataVenciment',
  v.Inhalt_Datum AS 'VencimentConfirmat',
  r.BezahlDatum AS 'DataPagat',
  CASE
    WHEN t1.Inhalt = 52 AND r.Status != 2 THEN r.preis /* 'BBVA' */
    ELSE ''
  END AS 'BBVA',
  CASE
    WHEN t1.Inhalt = 53 AND r.Status != 2 THEN r.preis /* 'Banc Sabadell' */
    ELSE ''
  END AS 'BANC SABADELL',
  CASE
    WHEN t1.Inhalt = 54 AND r.Status != 2 THEN r.preis /* 'LKXA' */
    ELSE ''
  END AS 'LA CAIXA',
  CASE
    WHEN t1.Inhalt = 73 AND r.Status != 2 THEN r.preis /* 'Banc Popular' */
    ELSE ''
  END AS 'BANC POPULAR',
  CASE
    WHEN t1.Inhalt = 104 AND r.Status != 2 THEN r.preis /* 'Bankinter' */
    ELSE ''
  END AS 'BANKINTER',
  CASE
    WHEN t1.Inhalt = 131 AND r.Status != 2 THEN r.preis /* 'BBVA GROUP' */
    ELSE ''
  END AS 'BBVA GROUP'			
FROM rechnungkunde r
LEFT JOIN Kunde k ON k.KundeID = r.IDKunde
LEFT JOIN Adresse a ON a.AdresseID = r.AnschriftID
LEFT JOIN kunde_textmodul t1 ON t1.IDTextModul = 19 AND t1.IDMain = r.IDKunde
LEFT JOIN kunde_textmodul t2 ON t2.IDTextModul = 22 AND t2.IDMain = r.IDKunde
LEFT JOIN w_land_zusatzsprache l ON l.LandID = a.IDLand AND DokSpracheID = 2 /* DokSpracheID = 2 en Español */
LEFT JOIN rechnungassistent c ON c.IDMain = r.RechnungKundeID AND c.ProjektRolleID = 3
LEFT JOIN mitarbeiter m ON m.MitarbeiterID = c.IDMitarbeiter
LEFT JOIN ausgangsrechnung_textmodul v ON v.IDTextModul = 31 AND r.RechnungKundeID = v.IDMain
WHERE YEAR(r.DatumRechnung) = $year
";
  try{
    $db = new db();
    $db = $db->conectDB();
    $result = $db->query($sqlPlunet);

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

// GET Presupuestos Expirados
$app->get('/api/presupuestos/expirados', function(Request $request, Response $response){
  $sqlPlunet = "SELECT 
    a.AnzeigeName AS 'NumPresupuesto', 
    CONCAT(m.Vorname, ' ', m.Nachname) AS 'GestionadoPor', 
    c.NachName AS 'Cliente',
    CASE
        WHEN k.`Inhalt` = 63 then 'BIG'
        WHEN k.`Inhalt` = 64 then 'LOYAL'
        WHEN k.`Inhalt` = 65 then 'POTENTIAL'
        WHEN k.`Inhalt` = 66 then 'INDIVIDUAL'
        WHEN k.`Inhalt` = 67 then 'PREMIUM'
        WHEN k.`Inhalt` = 68 then 'VIP'
        ELSE 'Desconocido'
    END AS 'TipoCliente',
    d.Ort AS 'Ciudad', 
    d.Bundesland AS 'Provincia', 
    a.DatumAngebot AS 'FechaCreacionPresupuesto', 
    ROUND(a.Preis + (a.MWStSatz1 * a.MWStBrutto1 / 100 * -1) + (a.MWStSatz2 * a.MWStBrutto2 / 100 * -1) + (a.MWStSatz3 * a.MWStBrutto3 / 100 * -1) + (a.MWStSatz4 * a.MWStBrutto4 / 100 * -1) + (a.MWStSatz5 * a.MWStBrutto5 / 100 * -1), 2) AS 'Precio',
    CASE
        WHEN a.`Status` = 1 then 'Presupuestado'
        WHEN a.`Status` = 6 then 'Expirado'
        ELSE 'Otro'
    END AS 'Estado',
    CASE
      WHEN t.`Inhalt` = 86 then 'Sí'
      ELSE 'No'
    END AS 'EnEspera',
    CONCAT(m2.Vorname, ' ', m2.Nachname) AS 'Comercial',
    z.Bez AS 'Zona'
    FROM mitarbeiter m2, Adresse d, Angebot a
    INNER JOIN (SELECT AngebotNr, MAX(Version) AS maxv FROM Angebot GROUP BY AngebotNr) maxi
    ON a.AngebotNr = maxi.AngebotNr AND a.Version = maxi.maxv
    LEFT JOIN Kunde c ON a.IDKunde = c.KundeID
    LEFT JOIN Angebot_textmodul t ON (t.`IDMain` = a.`AngebotID` AND t.`IDTextModul` = 23)
    LEFT JOIN kunde_textmodul k ON a.IDKunde = k.IDMain AND k.IDTextModul = 22
    LEFT JOIN angebotassistent s ON s.Status = 2 AND s.IDMain = a.AngebotID
    LEFT JOIN mitarbeiter m ON m.MitarbeiterID = s.IDMitarbeiter
    LEFT JOIN x_mandant z ON c.MandantID = z.MandantID
    WHERE (a.`Status` = 6 OR a.`Status` = 1) AND m2.MitarbeiterID = c.IDBetreuer AND d.IDKunde = a.IDKunde AND d.Typ = 1 AND ROUND(a.Preis + (a.MWStSatz1 * a.MWStBrutto1 / 100 * -1) + (a.MWStSatz2 * a.MWStBrutto2 / 100 * -1) + (a.MWStSatz3 * a.MWStBrutto3 / 100 * -1) + (a.MWStSatz4 * a.MWStBrutto4 / 100 * -1) + (a.MWStSatz5 * a.MWStBrutto5 / 100 * -1), 2) != 0
    ORDER BY d.Bundesland, a.DatumAngebot
  ";
  try{
    $db = new db();
    $db = $db->conectDB();
    $result = $db->query($sqlPlunet);

    if ($result->rowCount() > 0){
      $clientes = $result->fetchAll(PDO::FETCH_OBJ);
      echo json_encode($clientes);
    } else {
      echo json_encode("No existen Presupuestos Expirados en la BBDD");
    }
    $result = null;
    $db = null;
  } catch(PDOException $err) {
    echo '{"error" : {"text":'.$err->getMessage().'}';
  }
});