FROM serversideup/php:8.4-fpm-nginx

# Switch to root to install extensions
USER root

# Download mlocati's PHP extension installer helper (100% pre-compiled, fast, and no compile OOM failures)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Make the helper executable and install all required Laravel PHP extensions
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions pgsql pdo_pgsql pdo_mysql intl gd zip bcmath opcache

# Set document root to Laravel public folder
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Copy application files and set ownership to webuser (UID 1000)
COPY --chown=1000:1000 . /var/www/html

# Switch back to webuser (UID 1000)
USER 1000

# Run composer install to install dependencies
RUN composer install --no-dev --optimize-autoloader
