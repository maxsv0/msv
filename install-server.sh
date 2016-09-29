#!/bin/sh
echo "Installing MSV Server"

add-apt-repository universe
apt-get -y update 
apt-get -y install debconf-utils
apt-get -y install unzip 
apt-get -y install apache2 
apt-get -y install pwgen

MASTER_PASS=$(pwgen -c -1 12)
echo "Master password is: $MASTER_PASS"

echo "mysql-server mysql-server/root_password password $MASTER_PASS" | sudo debconf-set-selections
echo "mysql-server mysql-server/root_password_again password $MASTER_PASS" | sudo debconf-set-selections

apt-get -y install mysql-server mysql-client

mysql -h 127.0.0.1 -uroot -p$MASTER_PASS -e "CREATE USER 'msv'@'localhost' IDENTIFIED BY '$MASTER_PASS';"
mysql -h 127.0.0.1 -uroot -p$MASTER_PASS -e "GRANT USAGE ON *.* TO 'msv'@'localhost' IDENTIFIED BY '$MASTER_PASS' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;"
mysql -h 127.0.0.1 -uroot -p$MASTER_PASS -e "CREATE DATABASE IF NOT EXISTS 'msv';"
mysql -h 127.0.0.1 -uroot -p$MASTER_PASS -e "GRANT ALL PRIVILEGES ON msv.* TO 'msv'@'localhost';"


apt-get -y install php5
apt-get -y install libapache2-mod-php5
apt-get -y install php5-mysql
apt-get -y install php5-xml
apt-get -y install php5-curl 
apt-get -y install php-gettext

echo "proftpd-basic shared/proftpd/inetd_or_standalone select standalone" | sudo debconf-set-selections
apt-get -y install proftpd-basic  

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