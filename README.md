# MSV Website Framework 1.0.1 Beta

http://msvhost.com/

Open Source PHP Framework with build-in Content Management System (CMS) for website of any complexity.

####To install framework:####
1. Download latest archive or clone git repository
2. Install to local folder or run in Docker container



# Download

Copy framework to folder **msv**
```
git clone https://github.com/maxsv0/msv.git msv
cd msv
```


# Run in Docker container

Build docker image and run it
```
docker build  -t msv-app .   
docker run -d -p 80:80 msv-app
```

Note: MySQL is not included in Docker image.

MySQL can be started with command
```
docker run -e MYSQL_ROOT_PASSWORD=test -d -p 3306:3306 mysql
```



# Install to local folder

To copy website files to  **/var/www/html** run 
```
chmod +x install.sh 
./install.sh /var/www/html
```
Web root dir  **/var/www/html**

# Auto LAMP Server Installation
```
chmod +x install-server.sh 
./install-server.sh
```
LAMP server installation

