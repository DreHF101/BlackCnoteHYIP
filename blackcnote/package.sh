#!/bin/bash

# Create temporary directory
mkdir -p ../temp/blackcnote

# Copy theme files
cp -r * ../temp/blackcnote/

# Remove development files
rm -f ../temp/blackcnote/package.sh
rm -f ../temp/blackcnote/screenshot.txt

# Create zip file
cd ../temp
zip -r blackcnote.zip blackcnote

# Move zip to themes directory
mv blackcnote.zip ../themes/

# Clean up
rm -rf blackcnote
cd ../themes/blackcnote

echo "Theme packaged successfully as blackcnote.zip" 