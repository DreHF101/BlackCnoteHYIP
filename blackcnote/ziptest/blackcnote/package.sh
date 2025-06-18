#!/bin/bash

# Create temporary directory
mkdir -p ../temp/hyip-theme

# Copy theme files
cp -r * ../temp/hyip-theme/

# Remove development files
rm -f ../temp/hyip-theme/package.sh
rm -f ../temp/hyip-theme/screenshot.txt

# Create zip file
cd ../temp
zip -r hyip-theme.zip hyip-theme

# Move zip to themes directory
mv hyip-theme.zip ../themes/

# Clean up
rm -rf hyip-theme
cd ../themes/hyip-theme

echo "Theme packaged successfully as hyip-theme.zip" 