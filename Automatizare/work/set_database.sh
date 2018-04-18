#!/bin/bash
while true
do
	php -f /var/www/html/Automatizare/work/comparare.php;
	php -f /var/www/html/Automatizare/work/save_temps.php;

sleep 5
done
