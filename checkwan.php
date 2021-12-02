<?php

//Benodigde API en instellingen (graag nalopen!)
require_once('lib/Transip/DomainService.php');
define('DOMAIN', '###');

//TRANSIP Account
define('USERNAME', '###');
//API Key
define('PRIVATE_KEY', '-----BEGIN PRIVATE KEY-----
==PRIVATE KEY HERE==
-----END PRIVATE KEY-----');

date_default_timezone_set('Europe/Amsterdam');

//Haalt het externe adres op...
$ipAddress = file_get_contents('http://ipecho.net/plain');
echo "Huidig IP: ".$ipAddress . PHP_EOL;
$time = date('Y-m-d H:i:s', time());
file_put_contents("logging.txt", "". $time ." - Huidig IP ". $ipAddress ." \r\n", FILE_APPEND);
$newValues = [
'@' => $ipAddress,
];

//Verbinden met de TransIP API
Transip_ApiSettings::$login=USERNAME;
Transip_ApiSettings::$privateKey= PRIVATE_KEY;
$dnsEntries = Transip_DomainService::getInfo(DOMAIN)->dnsEntries;

$update = false;
$newDnsEntries = array();

//nieuwe array opbouwen op basis van oude data
foreach($dnsEntries as $dnsEntry){
  array_push($newDnsEntries, $dnsEntry);
  // @ Wildcard controleren. Als deze verschilt, moeten DNS-instellingen geupdated worden 
  if (($dnsEntry->type == Transip_DnsEntry::TYPE_A) && ($dnsEntry->name == '@') && $dnsEntry->content != $ipAddress){
	$dnsEntry->content = $ipAddress;
	$update=true;
  }
}

//Als het IP-adres geupdated moet worden
if ($update == true ){
  try{
	// Voer de update uit...
	Transip_DomainService::setDnsEntries(DOMAIN, $newDnsEntries);
	echo "DNS aangepast.";
	// Stukje logging naar een TXT bestand.
	$time = date('Y-m-d H:i:s', time());
	file_put_contents("logging.txt", "". $time ." - !!UPDATE UITGEVOERD!! --> Nieuw IP: ". $ipAddress ." \r\n", FILE_APPEND);
  }
  catch(SoapFault $f){
	// Fout?
	echo "Er is iets fout gegaan, DNS is niet geupdated... " . $f->getMessage();
  }
}

$time = date('Y-m-d H:i:s', time());
file_put_contents("logging.txt", "". $time ." - Check gedaan --> IP: ". $ipAddress ." \r\n", FILE_APPEND);
?>
