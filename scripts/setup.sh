#!/bin/bash
# Setup script for Limon ERP v2.0

echo "Limon ERP v2.0 - Setup"

if [ ! -f .env ]; then
    cp .env.example.new .env
    echo "✓ Created .env file"
fi

docker-compose -f docker-compose.new.yml up -d
echo "✓ Services started"
echo "Access at: http://localhost:3000"
