FROM wordpress:6.8-apache

# Copy custom Apache config
COPY blackcnote-wordpress.conf /etc/apache2/sites-available/blackcnote-wordpress.conf

RUN a2dissite 000-default && \
    a2ensite blackcnote-wordpress

# Expose port 80
EXPOSE 80

# Startup script to reload Apache at runtime
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"] 