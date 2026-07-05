#!/bin/bash

# Script to run integration tests with WordPress Docker container
# Usage: ./run-integration-tests.sh

set -e

echo "🚀 WordPress API SDK - Integration Tests"
echo "========================================"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

echo "📦 Starting WordPress container..."
docker compose up -d

echo "⏳ Waiting for WordPress to initialize (60 seconds)..."
sleep 60

echo "🔍 Checking if WordPress is accessible..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/wp-json | grep -q "200"; then
    echo "✅ WordPress is running!"
else
    echo "⚠️  WordPress may not be fully ready yet. Tests might be skipped."
fi

echo ""
echo "🧪 Running integration tests..."
echo "--------------------------------"
composer test:integration

echo ""
echo "📊 Test Summary:"
echo "- Integration tests check real API interactions"
echo "- Tests automatically skip if WordPress is not available"
echo ""

read -p "Do you want to stop WordPress? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🛑 Stopping WordPress container..."
    docker compose down
    echo "✅ WordPress stopped."
else
    echo "ℹ️  WordPress is still running. Stop it with: docker compose down"
fi

echo ""
echo "✨ Done!"
