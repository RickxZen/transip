# TransIP
As I don't have a static external IP address I use this script to check if my external IP address has changed and if that is true, it will use the TransIP API to change the DNS records for my domain.

TransIP is my domain host and has an extensive API available to manage a domain.

- API documentation TransIP: https://api.transip.nl/rest/docs.html
- To create an API key in TransIP use this link: https://www.transip.nl/cp/account/api/

## Usage
Follow instructions from: https://github.com/transip/transip-api-php#composer

In the checkwan script fill parameters for your account and domain. Change the 'domain.tld' on line 11 to your domain, change the 'username' on line 10 to your TransIP username and paste ur private key on line 18.

## Cron job
Using a cronjob I perform this script hourly on my QNAP NAS. 

0 * * * * /mnt/ext/opt/apache/bin/php -c /php.ini /path/to/file/checkwan.php 2> /path/to/file/cron_error.txt
