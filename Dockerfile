FROM webdevops/php-nginx:8.4

# Set document root for Nginx
ENV WEB_DOCUMENT_ROOT=/app/public
ENV APP_ENV=production

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Run composer install to install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and bootstrap cache
RUN chown -R application:application /app/storage /app/bootstrap/cache
