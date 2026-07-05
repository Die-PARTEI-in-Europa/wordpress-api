#!/bin/bash

# WordPress initialization script for testing
# This script sets up WordPress with test data for API testing

# Wait for WordPress to be ready
sleep 30

# Install WordPress CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mv wp-cli.phar /usr/local/bin/wp

# Install WordPress
wp core install \
  --url=http://localhost:8080 \
  --title="WordPress API Test Site" \
  --admin_user=admin \
  --admin_password=admin \
  --admin_email=admin@example.com \
  --allow-root

# Set pretty permalinks
wp rewrite structure '/%postname%/' --allow-root

echo "Creating structured test data for integration tests..."

# Create specific test posts (matching TestDataSeeder::TEST_POSTS)
wp post create \
  --post_title="Hello World" \
  --post_name="hello-world" \
  --post_content="Welcome to WordPress. This is your first post." \
  --post_status=publish \
  --allow-root

wp post create \
  --post_title="Sample Post" \
  --post_name="sample-post" \
  --post_content="This is a sample post for testing the WordPress API SDK." \
  --post_status=publish \
  --allow-root

wp post create \
  --post_title="Draft Post" \
  --post_name="draft-post" \
  --post_content="This is a draft post." \
  --post_status=draft \
  --allow-root

wp post create \
  --post_title="Test Pagination 1" \
  --post_name="test-pagination-1" \
  --post_content="First post for pagination testing." \
  --post_status=publish \
  --allow-root

wp post create \
  --post_title="Test Pagination 2" \
  --post_name="test-pagination-2" \
  --post_content="Second post for pagination testing." \
  --post_status=publish \
  --allow-root

wp post create \
  --post_title="Test Pagination 3" \
  --post_name="test-pagination-3" \
  --post_content="Third post for pagination testing." \
  --post_status=publish \
  --allow-root

# Create specific test pages (matching TestDataSeeder::TEST_PAGES)
wp post create \
  --post_type=page \
  --post_title="Sample Page" \
  --post_name="sample-page" \
  --post_content="This is a sample page." \
  --post_status=publish \
  --allow-root

wp post create \
  --post_type=page \
  --post_title="About Us" \
  --post_name="about-us" \
  --post_content="Learn more about our company." \
  --post_status=publish \
  --allow-root

wp post create \
  --post_type=page \
  --post_title="Contact" \
  --post_name="contact" \
  --post_content="Get in touch with us." \
  --post_status=publish \
  --allow-root

# Create additional posts for bulk testing
for i in {4..15}; do
  wp post create \
    --post_type=post \
    --post_title="Additional Post $i" \
    --post_content="This is additional post number $i for bulk testing." \
    --post_status=publish \
    --allow-root
done

# Create additional pages for bulk testing
for i in {4..8}; do
  wp post create \
    --post_type=page \
    --post_title="Additional Page $i" \
    --post_content="This is additional page number $i for bulk testing." \
    --post_status=publish \
    --allow-root
done

# Create categories
wp term create category "Technology" --slug=technology --allow-root
wp term create category "WordPress" --slug=wordpress --allow-root
wp term create category "PHP" --slug=php --allow-root

# Create tags
wp term create post_tag "API" --slug=api --allow-root
wp term create post_tag "REST" --slug=rest --allow-root
wp term create post_tag "SDK" --slug=sdk --allow-root

# Enable Application Passwords
wp plugin install application-passwords --activate --allow-root 2>/dev/null || true

# Install and configure Polylang for multilingual testing
echo "Installing Polylang for multilingual support..."
wp plugin install polylang --activate --allow-root

# Configure languages using Polylang
echo "Configuring Polylang languages..."
wp pll lang create en "English" en_US --allow-root 2>/dev/null || echo "English already exists"
wp pll lang create de "Deutsch" de_DE --allow-root 2>/dev/null || echo "German already exists"
wp pll lang create fr "Français" fr_FR --allow-root 2>/dev/null || echo "French already exists"

# Set English as default language
wp pll option set default_lang en --allow-root 2>/dev/null || true

echo "Creating multilingual test posts with Polylang..."

# Create posts in different languages
# Note: With WPML, language association is typically done via post meta or WPML API
# For now, we create posts with clear language indicators in slugs

# English posts
POST_EN=$(wp post create \
  --post_title="Hello World English" \
  --post_name="hello-world-en" \
  --post_content="Welcome to WordPress. This is your first post in English." \
  --post_status=publish \
  --porcelain \
  --allow-root)

# German posts
POST_DE=$(wp post create \
  --post_title="Hallo Welt" \
  --post_name="hallo-welt" \
  --post_content="Willkommen bei WordPress. Dies ist dein erster Beitrag auf Deutsch." \
  --post_status=publish \
  --porcelain \
  --allow-root)

# French posts
POST_FR=$(wp post create \
  --post_title="Bonjour le monde" \
  --post_name="bonjour-le-monde" \
  --post_content="Bienvenue sur WordPress. Ceci est votre premier article en français." \
  --post_status=publish \
  --porcelain \
  --allow-root)

# Technology posts in multiple languages
POST_TECH_EN=$(wp post create \
  --post_title="Technology Post" \
  --post_name="technology-post-en" \
  --post_content="This is a post about technology in English." \
  --post_status=publish \
  --porcelain \
  --allow-root)

POST_TECH_DE=$(wp post create \
  --post_title="Technologie-Beitrag" \
  --post_name="technologie-beitrag" \
  --post_content="Dies ist ein Beitrag über Technologie auf Deutsch." \
  --post_status=publish \
  --porcelain \
  --allow-root)

# Multilingual pages
PAGE_ABOUT_EN=$(wp post create \
  --post_type=page \
  --post_title="About Us" \
  --post_name="about-us-en" \
  --post_content="Learn more about our company." \
  --post_status=publish \
  --porcelain \
  --allow-root)

PAGE_ABOUT_DE=$(wp post create \
  --post_type=page \
  --post_title="Über uns" \
  --post_name="ueber-uns" \
  --post_content="Erfahren Sie mehr über unser Unternehmen." \
  --post_status=publish \
  --porcelain \
  --allow-root)

# Link translations using Polylang
echo "Linking translations in Polylang..."
wp pll post set $POST_EN de $POST_DE --allow-root 2>/dev/null || echo "Could not link EN-DE translations"
wp pll post set $POST_EN fr $POST_FR --allow-root 2>/dev/null || echo "Could not link EN-FR translations"
wp pll post set $POST_TECH_EN de $POST_TECH_DE --allow-root 2>/dev/null || echo "Could not link Tech EN-DE"
wp pll post set $PAGE_ABOUT_EN de $PAGE_ABOUT_DE --allow-root 2>/dev/null || echo "Could not link Page EN-DE"

# Set language for each post
wp pll post set-language $POST_EN en --allow-root 2>/dev/null || true
wp pll post set-language $POST_DE de --allow-root 2>/dev/null || true
wp pll post set-language $POST_FR fr --allow-root 2>/dev/null || true
wp pll post set-language $POST_TECH_EN en --allow-root 2>/dev/null || true
wp pll post set-language $POST_TECH_DE de --allow-root 2>/dev/null || true
wp pll post set-language $PAGE_ABOUT_EN en --allow-root 2>/dev/null || true
wp pll post set-language $PAGE_ABOUT_DE de --allow-root 2>/dev/null || true

echo ""
echo "==========================================="
echo "WordPress initialized successfully!"
echo "==========================================="
echo "URL: http://localhost:8080"
echo "Admin User: admin"
echo "Admin Password: admin"
echo ""
echo "Multilingual Support: Polylang (Free & Open Source)"
echo "Languages: English (en), German (de), French (fr)"
echo "Default Language: English"
echo ""
echo "Test Data Created:"
echo "- 6 specific test posts"
echo "- 12 additional posts"
echo "- 3 specific test pages"
echo "- 5 additional pages"
echo "- Multilingual posts in EN/DE/FR"
echo "==========================================="
