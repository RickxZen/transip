<?php
use Transip\Api\Library\TransipAPI;
use Transip\Api\Library\Entity\Domain\DnsEntry;

require_once(__DIR__ . '/vendor/autoload.php');

date_default_timezone_set('Europe/Amsterdam');

//Your login name on the TransIP website.
$login = 'username';
$domainName = 'domain.tld';

//If the generated token should only be usable by whitelisted IP addresses in your Controlpanel
$generateWhitelistOnlyTokens = false;

//One of your private keys; these can be requested via your Controlpanel
$privateKey = '-----BEGIN PRIVATE KEY-----
==PRIVATE KEY HERE==
-----END PRIVATE KEY-----';

$api = new TransipAPI(
    $login,
    $privateKey,
    $generateWhitelistOnlyTokens
);

//Create a test connection to the api
$response = $api->test()->test();

if ($response === true) {
    echo 'API connection successful!'." \r\n";
}

//Get external IP address
$ipAddress = file_get_contents('http://ipecho.net/plain');
echo "Current external IP: ".$ipAddress . PHP_EOL;
$time = date('Y-m-d H:i:s', time());
file_put_contents("logging.txt", "". $time ." - Current external IP ". $ipAddress ." \r\n", FILE_APPEND);

$newDnsEntries = array();

//Get all DNS records
$dnsEntries = $api->domainDns()->getByDomainName($domainName);

//Loop through array and update all A records where IP address is old
foreach($dnsEntries as $dnsEntry){
  array_push($newDnsEntries, $dnsEntry);
  if (($dnsEntry->getType() == 'A') && $dnsEntry->getContent() != $ipAddress){
    //Set new external IP address on record
	  $dnsEntry->setContent($ipAddress);
    //Update record with new external IP address
	  $api->domainDns()->updateEntry($domainName, $dnsEntry);
    echo "Record changed"." \r\n";
  	//Logging
  	$time = date('Y-m-d H:i:s', time());
  	file_put_contents("logging.txt", "". $time ." - !!UPDATE PERFORMED!! --> New IP: ". $ipAddress ." \r\n", FILE_APPEND);
  }
}
?>
