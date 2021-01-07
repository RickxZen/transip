<?php

//Benodigde API en instellingen (graag nalopen!)
require_once('lib/Transip/DomainService.php');
define('DOMAIN', '###');

//TransIP Account
define('USERNAME', '###');
//TransIP API Key
define('PRIVATE_KEY', '-----BEGIN PRIVATE KEY-----
==PLAK PRIVATE KEY HIER==
-----END PRIVATE KEY-----');

date_default_timezone_set('Europe/Amsterdam');

//Haal huidige externe IP adres op
$ipAddress = file_get_contents('http://ipecho.net/plain');
echo "Huidig IP: ".$ipAddress . PHP_EOL;
//Logging
$time = date('Y-m-d H:i:s', time());
file_put_contents("logging.txt", "". $time ." - Huidig IP ". $ipAddress ." \r\n", FILE_APPEND);
$newValues = [
'@' => $ipAddress,
];

//Verbinden met de TransIP API
Transip_ApiSettings::$login=USERNAME;
Transip_ApiSettings::$privateKey=PRIVATE_KEY;

//Stop huidige DNS entries in variabele
$dnsEntries = Transip_DomainService::getInfo(DOMAIN)->dnsEntries;

$update = false;
$newDnsEntries = array();

//Nieuwe array opbouwen op basis van oude data
foreach($dnsEntries as $dnsEntry){
  array_push($newDnsEntries, $dnsEntry);
  //@ Wildcard controleren. Als deze verschilt, moeten DNS-instellingen geüpdatet worden 
  if (($dnsEntry->type == Transip_DnsEntry::TYPE_A) && ($dnsEntry->name == '@') && $dnsEntry->content != $ipAddress){
	$dnsEntry->content = $ipAddress;
	$update=true;
  }
}

//Als het IP adres geüpdatet moet worden
if ($update == true ){
  try{
	//DNS records toevoegen
	Transip_DomainService::setDnsEntries(DOMAIN, $newDnsEntries);
	echo "DNS aangepast.";
	//Logging
	$time = date('Y-m-d H:i:s', time());
	file_put_contents("logging.txt", "". $time ." - !!UPDATE UITGEVOERD!! --> Nieuw IP: ". $ipAddress ." \r\n", FILE_APPEND);
  }
  catch(SoapFault $f){
	//Fout?
	echo "Er is iets fout gegaan, DNS is niet geupdated... " . $f->getMessage();
  }
}

//Logging
$time = date('Y-m-d H:i:s', time());
file_put_contents("logging.txt", "". $time ." - Check gedaan --> IP: ". $ipAddress ." \r\n", FILE_APPEND);
?>