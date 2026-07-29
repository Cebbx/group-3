FROM serversideup/php:8.4-fpm-nginx

# Switch to root to install PostgreSQL extension and configure permissions
USER root

# Install pgsql PDO driver since Render free database is PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql

# Set document root to public folder
ENV AUTORUN_ENABLED=true
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Copy the application code
COPY --chown=1000:1000 . /var/www/html

# Switch back to webuser (UID 1000)
USER 1000

# Install composer packages
RUN composer install --no-dev --optimize-autoloader

# Expose Nginx port
EXPOSE 8080
