#!/bin/bash

echo "UPDATE WEBSITE"


cp -R src/include/* /var/www/html/include/

cp -R src/content/* /var/www/html/content/

cp -R src/templates/* /var/www/html/templates/

cp -R src/index.php /var/www/html/index.php

cp -R src/load.php /var/www/html/load.php
