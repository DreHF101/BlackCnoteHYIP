#!/bin/bash

# BlackCnote Theme Packaging Script
# This script creates a ZIP file of the theme for distribution

# Create temporary directory
mkdir -p ../temp/blackcnote

# Copy files (excluding ziptest and development files)
rsync -av --exclude='ziptest' --exclude='.git' --exclude='package.sh' --exclude='package.ps1' --exclude='*.log' --exclude='*.zip' . ../temp/blackcnote/

# Create zip file
cd ../temp
zip -r blackcnote.zip blackcnote

# Move to themes directory
mv blackcnote.zip ../themes/

# Clean up
rm -rf blackcnote
cd ../themes/blackcnote

echo "BlackCnote Theme packaged successfully as blackcnote.zip"
echo "Ready for deployment to WordPress.com or GitHub!" 