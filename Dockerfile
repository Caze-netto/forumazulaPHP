FROM php:8.2-apache

# Ativar mod_rewrite e configurar ServerName
RUN a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Instalar driver PDO para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Copiar código fonte
COPY src/ /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configuração do Apache para index.php
RUN echo "<Directory /var/www/html>\n\
    Options -Indexes +FollowSymLinks\n\
    DirectoryIndex index.php index.html\n\
    AllowOverride All\n\
</Directory>" > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

EXPOSE 80

CMD ["apache2-foreground"]