FROM php:8.2-fpm-alpine

# Install Nginx
RUN apk add --no-cache nginx

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

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD wget --no-verbose --tries=1 --spider http://localhost/ || exit 1

EXPOSE 80

CMD ["/start.sh"]
