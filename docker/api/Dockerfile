FROM php:8.4-apache

# Extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Módulos Apache necesarios para .htaccess y headers
RUN a2enmod rewrite headers

# Copiar configuración virtual host
COPY docker/api/vhost.conf /etc/apache2/sites-available/webadmin-api.conf

# Copiar la API completa
COPY server/api/api/ /var/www/webadmin-api/

# Sustituir únicamente dentro de la imagen Docker
# la configuración de base de datos del servidor.
COPY docker/api/database.php /var/www/webadmin-api/config/database.php

# Activar el virtual host
RUN a2dissite 000-default.conf \
    && a2ensite webadmin-api.conf

# Permisos
RUN chown -R www-data:www-data /var/www/webadmin-api \
    && find /var/www/webadmin-api -type d -exec chmod 755 {} \; \
    && find /var/www/webadmin-api -type f -exec chmod 644 {} \;

WORKDIR /var/www/webadmin-api/public