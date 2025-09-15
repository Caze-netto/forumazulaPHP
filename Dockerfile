FROM php:8.2-apache

RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

COPY src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN echo "<Directory /var/www/html>\n\
    Options -Indexes +FollowSymLinks\n\
    DirectoryIndex index.php index.html\n\
    AllowOverride All\n\
</Directory>" > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

EXPOSE 80

CMD ["apache2-foreground"]