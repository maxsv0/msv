# MSV Website PHP Framework 1.0. Beta

http://msv.host/

Open Source PHP Web Framework with build-in CMS (Content Management System) for website of any complexity.


# Coping

git clone https://github.com/maxsv0/msv.git msv 

cd msv


# Installation

chmod +x install.sh && ./install.sh /var/www/html


# Run with Docker

docker build  -t msv-app .   
docker run -d -p 80:80 msv-app

Note: MySQL is not included in Docker image.

# Automated Server Installation

chmod +x install-server.sh && ./install-server.sh
