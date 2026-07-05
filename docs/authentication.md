# Authentication

WordPress Application Passwords (available since WordPress 5.6) are the recommended and secure way to authenticate with the WordPress REST API. They allow you to create unique passwords for applications without exposing your main WordPress password.

## How to create an Application Password in WordPress

1. Log in to your WordPress admin panel
2. Go to **Users > Profile** (or **Users > Your Profile**)
3. Scroll down to the **Application Passwords** section
4. Enter a name for your application (e.g., "My PHP SDK")
5. Click **Add New Application Password**
6. Copy the generated password immediately (it will only be shown once!)
7. The password format is: `xxxx xxxx xxxx xxxx xxxx xxxx` (24 characters with spaces)

## Using Application Passwords in the SDK

```php
// Application Passwords (WordPress 5.6+) - Recommended
$client = new Client('https://example.com', [
    'auth' => ['username', 'xxxx xxxx xxxx xxxx xxxx xxxx']
]);

// Real example (the password is shown only once when created):
$client = new Client('https://example.com', [
    'auth' => ['admin', 'abcd 1234 efgh 5678 ijkl 9012']
]);

// Spaces in the Application Password are optional
$client = new Client('https://example.com', [
    'auth' => ['admin', 'abcd1234efgh5678ijkl9012']
]);
```

## Benefits of Application Passwords
- More secure than using your main WordPress password
- Can be revoked individually without changing your main password
- Each application can have its own unique password
- Easy to manage and track which applications have access
- No need to share your actual WordPress password
