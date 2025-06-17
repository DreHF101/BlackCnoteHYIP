# Create screenshot for BlackCnote Theme
$text = "BlackCnote Theme for WordPress"
$font = "Arial"
$size = 48
$color = "White"
$background = "Black"
$width = 1200
$height = 900

# Create image
$bitmap = New-Object System.Drawing.Bitmap $width, $height
$graphics = [System.Drawing.Graphics]::FromImage($bitmap)
$graphics.Clear($background)

# Set up text formatting
$fontFamily = New-Object System.Drawing.FontFamily $font
$font = New-Object System.Drawing.Font $fontFamily, $size
$brush = New-Object System.Drawing.SolidBrush $color
$stringFormat = New-Object System.Drawing.StringFormat
$stringFormat.Alignment = [System.Drawing.StringAlignment]::Center
$stringFormat.LineAlignment = [System.Drawing.StringAlignment]::Center

# Draw text
$graphics.DrawString($text, $font, $brush, $width/2, $height/2, $stringFormat)

# Save image
$bitmap.Save("screenshot.png", [System.Drawing.Imaging.ImageFormat]::Png)

# Clean up
$graphics.Dispose()
$bitmap.Dispose() 