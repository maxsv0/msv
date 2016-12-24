FROM php:7.0-apache
MAINTAINER me

COPY src/ /var/www/html/

RUN find /var/www/html -type f  -exec chmod 666 {} \; \	
	&& find /var/www/html -type d -exec chmod 777 {} \; 

RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

RUN a2enmod rewrite
RUN a2enmod headers 
RUN a2enmod expires 
RUN a2enmod deflate 

# Install other needed extensions
RUN apt-get update && apt-get install -y libfreetype6 libjpeg62-turbo libmcrypt4 libpng12-0 --no-install-recommends && rm -rf /var/lib/apt/lists/*
RUN buildDeps=" \
		libfreetype6-dev \
		libjpeg-dev \
		libmcrypt-dev \
		libpng12-dev \
		zlib1g-dev \
	"; \
	set -x \
	&& apt-get update && apt-get install -y $buildDeps --no-install-recommends && rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --enable-gd-native-ttf --with-jpeg-dir=/usr/lib/x86_64-linux-gnu --with-png-dir=/usr/lib/x86_64-linux-gnu --with-freetype-dir=/usr/lib/x86_64-linux-gnu \
	&& docker-php-ext-install gd exif mbstring mcrypt mysqli zip \
	&& apt-get purge -y --auto-remove $buildDeps


		
		
		
RUN echo "file_uploads = On\n" \
         "memory_limit = 500M\n" \
         "upload_max_filesize = 200M\n" \
         "post_max_size = 200M\n" \
         > /usr/local/etc/php/conf.d/msv.ini


RUN apachectl restart
