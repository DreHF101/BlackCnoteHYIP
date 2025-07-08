#!/bin/bash
set -e

# Enable the custom site
a2ensite blackcnote-wordpress

# Start Apache in the foreground
exec apache2-foreground 