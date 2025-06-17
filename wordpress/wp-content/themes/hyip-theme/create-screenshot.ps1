# PowerShell script to create theme screenshot
Add-Type -AssemblyName System.Drawing

# Create a new bitmap
$bitmap = New-Object System.Drawing.Bitmap 1200, 900
$graphics = [System.Drawing.Graphics]::FromImage($bitmap)

# Fill background
$graphics.Clear([System.Drawing.Color]::White)

# Load and draw logo
$logo = [System.Drawing.Image]::FromFile("assets\images\BLACKCNOTE Logo.png")
$logoWidth = 400
$logoHeight = [int]($logo.Height * ($logoWidth / $logo.Width))
$logoX = (1200 - $logoWidth) / 2
$logoY = (900 - $logoHeight) / 2
$graphics.DrawImage($logo, $logoX, $logoY, $logoWidth, $logoHeight)

# Add text
$font = New-Object System.Drawing.Font("Arial", 24)
$brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::Black)
$text = "HYIP Theme for WordPress"
$textSize = $graphics.MeasureString($text, $font)
$textX = (1200 - $textSize.Width) / 2
$textY = $logoY + $logoHeight + 20
$graphics.DrawString($text, $font, $brush, $textX, $textY)

# Save the image
$bitmap.Save("screenshot.png", [System.Drawing.Imaging.ImageFormat]::Png)

# Clean up
$graphics.Dispose()
$bitmap.Dispose()
$logo.Dispose() 