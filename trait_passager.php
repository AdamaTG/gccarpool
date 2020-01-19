<?php
include('config.php');

session_start();

function custom_base_62($n)
{

    $n ++;
    return ($n*5%13).$n.($n/12%15).($n+5);
}


//Nombre de place de chaque véhicule




 $NomPass=htmlspecialchars($_POST['nom'], ENT_QUOTES);
 $PrenPass=htmlspecialchars($_POST['pren'], ENT_QUOTES);
 $AdrPass=htmlspecialchars($_POST['adr'], ENT_QUOTES);
 $Email=$_POST['mail'];
 $Numero=$_POST['num'];
 $CodeTrajet=$_POST['ligne'];
 $CodeGare=$_POST['gare'];


 $query="SELECT COUNT(*) as tot from passager ";
$result = $mysqli->query($query) or die(mysql_error());
$resultat = $result->fetch_array(MYSQLI_ASSOC);



      $sql=("INSERT  INTO passager (NomPass, PrenPass, AdrPass, Email, Numero, UniqueCode) 
      	VALUES ('$NomPass', '$PrenPass', '$AdrPass', '$Email', '228$Numero', '".custom_base_62($resultat['tot'])."')");
     $mysqli->query($sql);

       $sq =("SELECT max(CodePassager) as CodePassager from passager");
  $res1 = $mysqli->query($sq);
  $show1 = $res1->fetch_array(MYSQLI_ASSOC);
  $pass=$show1['CodePassager'];

      $sqll=("INSERT INTO ticket (CodePassager, CodeTrajet, CodeGare, DateAchat) 
      	VALUES ('$pass', '$CodeTrajet', '$CodeGare', NOW())");
     $mysqli->query($sqll);

echo "Success";


$sql2 = ("SELECT VilleDepart, VilleArrive, DateDepart 
        from trajet
        where CodeTrajet = $CodeTrajet ");
    $mont= $mysqli->query($sql2);
    $affi = $mont->fetch_array(MYSQLI_ASSOC);
    $dep=$affi['VilleDepart'];
    $arri=$affi['VilleArrive'];
    $datdep=$affi['DateDepart'];


         $sq =("SELECT Numero from passager Where CodePassager>=ALL(SELECT CodePassager from passager)");
  $res1 = $mysqli->query($sq);
  $show1 = $res1->fetch_array(MYSQLI_ASSOC);
  $tel=$show1['Numero']; 

try {
$serverUrl = "http://aspsmsapi.com/http/sendsms.aspx?"; // URL de base
$dest = $tel; // Numéro du destinataire au foramt international
$username = "22892431923"; // Votre nom d'utilisateur
$apikey = "BLCFW1D96C"; // Votre clé API
$msg = "Vous avez effectué une réservation sur ".$dep."-".$arri." le ".$datdep.". Code Ticket: ".custom_base_62($resultat['tot']); // Contenu du message
$senderid = "RapiDo"; // Identifiant d'envoi
$authmode = "http"; // Obligatoire. Ne pas modifier
// CURL_INIT
$ch = curl_init($serverUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS,"dest=$dest&username=$username&apikey=$apikey&senderid=$senderid&msg=$msg&authmode=$authmode");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$output = curl_exec($ch); // Afficher le résultat du serveur
curl_close($ch);

}catch(Exception $ex) {
echo $ex;
}


 ?>