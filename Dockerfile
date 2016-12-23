FROM php:7.0-apache
MAINTAINER me


COPY src/ /var/www/html/

RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

RUN a2enmod rewrite
RUN a2enmod headers 
RUN a2enmod expires 
RUN a2enmod deflate 


RUN docker-php-ext-install mysqli 


RUN echo "file_uploads = On\n" \
         "memory_limit = 500M\n" \
         "upload_max_filesize = 200M\n" \
         "post_max_size = 200M\n" \
         > /usr/local/etc/php/conf.d/msv.ini


RUN apachectl restart
