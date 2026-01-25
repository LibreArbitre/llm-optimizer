FROM php:8.2-fpm-alpine

# Install Nginx and wget for healthcheck
RUN apk add --no-cache nginx wget

# Create necessary directories
RUN mkdir -p /run/nginx /var/www/html

# Copy configuration files
COPY nginx.conf /etc/nginx/nginx.conf
COPY start.sh /start.sh

# Copy application
COPY index.php /var/www/html/

# Set permissions
RUN chmod +x /start.sh && \
    chown -R www-data:www-data /var/www/html

# Healthcheck - wait longer for services to start
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD wget --no-verbose --tries=1 --spider http://localhost:80/ || exit 1

EXPOSE 80

CMD ["/start.sh"]