<?php
// Contact form configuration
return [
    // If you want the site to send real emails, set smtp_enabled to true and configure SMTP below.
    'smtp_enabled' => false,
    'smtp' => [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'user@example.com',
        'password' => 'yourpassword',
        'secure' => 'tls', // 'ssl' or 'tls' or ''
        'from_email' => 'no-reply@vibeproperties.com',
        'from_name' => 'Vibe Properties',
        'to_email' => 'contact@vibeproperties.com',
    ],

    // Admin basic auth for viewing messages (choose a strong password).
    'admin_user' => 'admin',
    'admin_pass' => 'changeme',
];
