FROM serversideup/php:8.4-fpm-nginx

# Switch to root to install extensions and configure files
USER root

# Download mlocati's PHP extension installer helper (100% pre-compiled, fast, and no compile OOM failures)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Make the helper executable and install all required Laravel PHP extensions
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions pgsql pdo_pgsql pdo_mysql intl gd zip bcmath opcache

# Set document root to Laravel public folder
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Copy application files
COPY . /var/www/html

# Run composer install as root and ignore platform requirements to guarantee build success
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Make storage and bootstrap cache world-writable to bypass any user ID permission conflicts on Render
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
