FROM php:8.4.24-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite headers

COPY apache/vhost.conf /etc/apache2/sites-available/webadmin.conf
COPY apache/security.conf /etc/apache2/conf-available/webadmin-security.conf

COPY app/ /var/www/webadmin/

RUN a2dissite 000-default.conf \
    && a2ensite webadmin.conf \
    && a2enconf webadmin-security

RUN chown -R root:www-data /var/www/webadmin \
    && find /var/www/webadmin -type d -exec chmod 750 {} \; \
    && find /var/www/webadmin -type f -exec chmod 640 {} \; \
    && chmod 755 /var/www/webadmin/public