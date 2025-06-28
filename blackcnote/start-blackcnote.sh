#!/bin/bash
set -e

# Detect if running in WSL2
if grep -qEi "(Microsoft|WSL)" /proc/version &> /dev/null; then
  echo "[BlackCnote] Detected WSL2 environment."
  # Sync React app files from Windows if needed
  WIN_REACT_APP="/mnt/c/Users/CASH AMERICA PAWN/Desktop/BlackCnote/react-app"
  LINUX_REACT_APP="$HOME/blackcnote/react-app"
  if [ -d "$WIN_REACT_APP" ]; then
    echo "[BlackCnote] Syncing React app files from Windows to WSL2..."
    mkdir -p "$LINUX_REACT_APP"
    cp -ru "$WIN_REACT_APP/"* "$LINUX_REACT_APP/"
  fi
  # Start WSL2 Docker Compose
  cd "$HOME/blackcnote"
  echo "[BlackCnote] Starting Docker Compose (WSL2)..."
  docker-compose -f config/docker/docker-compose-wsl2.yml up -d
else
  echo "[BlackCnote] Not running in WSL2. Please use the Windows PowerShell script."
  exit 1
fi 