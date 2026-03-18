# 1. Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# 2. Installer les dépendances système et l'extension MongoDB
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip libpng-dev libicu-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install zip gd intl opcache

# 3. Configurer Apache pour pointer sur le dossier /public de Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# 4. Copier le code du projet
COPY . /var/www/html
WORKDIR /var/www/html

# 5. Installer Composer et les dépendances
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV APP_RUNTIME_ENV=prod
ENV APP_ENV=prod

RUN composer install --no-dev --optimize-autoloader

# 6. Compiler les assets
RUN php bin/console asset-map:compile

# 7. Donner les droits au serveur web
RUN chown -R www-data:www-data /var/www/html/var