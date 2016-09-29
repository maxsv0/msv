#!/bin/sh
echo "Installing MSV Server"

apt-get -y update 
apt-get -y install debconf-utils
apt-get -y install unzip 
apt-get -y install apache2 
apt-get -y install pwgen

MASTER_PASS=$(pwgen -c -1 12)
echo "Master password is: $MASTER_PASS"

echo "mysql-server mysql-server/root_password password $MASTER_PASS" | sudo debconf-set-selections
echo "mysql-server mysql-server/root_password_again password $MASTER_PASS" | sudo debconf-set-selections

apt-get -y install mysql-server

apt-get -y install php5
apt-get -y install libapache2-mod-php5
apt-get -y install php-mysql
apt-get -y install php-xml
apt-get -y install php-mbstring
apt-get -y install php5-curl 
apt-get -y install php-gettext

apt-get -y install proftpd  

rm /var/www/html/index.html 

a2enmod rewrite 
a2enmod headers 
a2enmod expires 
a2enmod mcrypt 

printf "<Directory /var/www/>\n
        Options Indexes FollowSymLinks\n
        AllowOverride All\n
</Directory>\n">>/etc/apache2/apache2.conf 


printf "<Global>\n
    RootLogin	off\n
    RequireValidShell off\n
</Global>\n
<Limit LOGIN>\n
    DenyUser !devftp\n
</Limit>\n
DefaultRoot  ~\n">>/etc/proftpd/proftpd.conf 


wget https://dl-ssl.google.com/dl/linux/direct/mod-pagespeed-stable_current_amd64.deb 
dpkg -i mod-pagespeed-*.deb 

service apache2 restart 
service proftpd restart


adduser --disabled-password --gecos "" devftp -shell /bin/false -home /var/www 

echo devftp:$MASTER_PASS | chpasswd

./install.sh /var/www/html

chown -R devftp:www-data /var/www/html


echo "Master password is: $MASTER_PASS"