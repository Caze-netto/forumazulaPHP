# 1️⃣ Imagem base oficial do PHP com Apache
FROM php:8.2-apache

# 2️⃣ Ativar módulos do Apache
RUN a2enmod rewrite

# 3️⃣ Configurar ServerName para evitar aviso
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 4️⃣ Copiar código fonte
COPY src/ /var/www/html/

# 5️⃣ Dar permissão adequada
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 6️⃣ Configurar Apache para procurar index.php primeiro e permitir .htaccess
RUN echo "<Directory /var/www/html>\n\
    Options -Indexes +FollowSymLinks\n\
    DirectoryIndex index.php index.html\n\
    AllowOverride All\n\
</Directory>" > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# 7️⃣ Expor porta 80
EXPOSE 80

# 8️⃣ Iniciar Apache em foreground
CMD ["apache2-foreground"]