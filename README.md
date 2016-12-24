# MSV Website PHP Framework 1.0. Beta

http://msvhost.com/

Open Source PHP Framework with build-in Content Management System (CMS) for website of any complexity.
Enterprise resource planning (ERP) is in alpha test.


# Copying

```
git clone https://github.com/maxsv0/msv.git msv
cd msv
```


# Run in Docker container

```
docker build  -t msv-app .   
docker run -d -p 80:80 msv-app
```

Note: MySQL is not included in Docker image.

MySQL can be started with command:
```
docker run -e MYSQL_ROOT_PASSWORD=test -d -p 3306:3306 mysql
```



# Install to local folder

```
chmod +x install.sh 
./install.sh /var/www/html
```
Website will be installed to **/var/www/html**

# Automated Server Installation
```
chmod +x install-server.sh 
./install-server.sh
```
LAMP server installation

