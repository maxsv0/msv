# MSV Website PHP Framework 1.0. Beta

http://msv.host/

Open Source PHP Web Framework with build-in CMS (Content Management System) for website of any complexity.


# Installation

git clone https://github.com/maxsv0/msv.git /var/www/dev

cp -ar /var/www/dev/. /var/www/html

find /var/www -type d -exec chmod 775 {} \;  

find /var/www -type f -exec chmod 664 {} \;

chmod 777 /var/www/html/include/custom/smarty/cache

# Automated Installation

bash <(curl -s http://msv.host/content/install.sh)
