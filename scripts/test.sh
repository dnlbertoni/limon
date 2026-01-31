#!/bin/bash
# Test script for Limon ERP v2.0

echo "Limon ERP v2.0 - Test Suite"
echo "Testing basic functionality..."

# Test backend health
if curl -s http://localhost:8000/health | grep -q "healthy"; then
    echo "✓ Backend is healthy"
else
    echo "✗ Backend health check failed"
fi
