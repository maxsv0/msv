# MSV Website PHP Framework 1.0. Beta

http://msvhost.com/

Open Source PHP Framework with build-in Content Management System (CMS) for website of any complexity.
Enterprise resource planning (ERP) is in alpha test.


# Coping

git clone https://github.com/maxsv0/msv.git msv 

cd msv

# Run in Docker container

docker build  -t msv-app .   
docker run -d -p 80:80 msv-app

Note: MySQL is not included in Docker image.

# Install to /var/www/html

chmod +x install.sh 
./install.sh /var/www/html

# Automated Server Installation

chmod +x install-server.sh && ./install-server.sh
