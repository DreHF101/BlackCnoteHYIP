#!/bin/bash

# Create temporary directory
mkdir -p ../temp/blackcnote-theme

# Copy files
cp -r * ../temp/blackcnote-theme/

# Remove unnecessary files
rm -f ../temp/blackcnote-theme/package.sh
rm -f ../temp/blackcnote-theme/screenshot.txt

# Create zip file
cd ../temp
zip -r blackcnote-theme.zip blackcnote-theme

# Move to themes directory
mv blackcnote-theme.zip ../themes/

# Clean up
rm -rf blackcnote-theme
cd ../themes/blackcnote-theme

echo "Theme packaged successfully as blackcnote-theme.zip" 