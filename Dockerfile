FROM php:8.1-apache

# Extensión MySQL que usa la app
RUN docker-php-ext-install mysqli

# Composer (desde la imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

# Dependencias PHP (sin las de desarrollo) + URLs limpias
RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && a2enmod rewrite

# Render asigna el puerto en $PORT; Apache debe escuchar ahí (80 en local).
CMD sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf \
 && sed -i "s/:80>/:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf \
 && apache2-foreground
