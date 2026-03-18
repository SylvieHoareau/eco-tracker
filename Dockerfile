# Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# Installer les dépendances système et l'extension MongoDB
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libicu-dev \
    libssl-dev \
    pkg-config \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install zip gd intl opcache

# Configurer Apache pour pointer sur le dossier /public de Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copier le code du projet
COPY . /var/www/html
WORKDIR /var/www/html

# Installer Composer et les dépendances
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer les variables d'environnement pour Symfony
ENV APP_ENV=prod
# On définit des valeurs par défaut pour que le build ne plante pas
# Render écrasera ces valeurs au démarrage avec mes vrais secrets
ENV MONGODB_URI="mongodb://localhost:27017"
ENV MONGODB_DB="eco_tracker"

# Créer un fichier .env avec les variables d'environnement pour Symfony
RUN echo "APP_ENV=prod" > .env

# Installer les dépendances de production avec Composer
RUN composer install --no-dev --optimize-autoloader

# Lancer les scripts post-installation de Composer (ex: cache:clear, assets:install)
RUN composer run-script post-install-cmd

# Compiler les assets
RUN php bin/console asset-map:compile

# Donner les droits au serveur web
RUN chown -R www-data:www-data /var/www/html/var