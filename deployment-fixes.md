# Laravel API 404 Fix for Production Server

## 1. Clear Route Cache (Run on server)
```bash
php artisan route:clear
php artisan route:cache
php artisan config:clear
php artisan config:cache
```

## 2. Check Apache/Nginx Configuration

### For Apache (CyberPanel usually uses Apache)
Ensure your virtual host has:
```apache
<Directory "/path/to/your/laravel/public">
    AllowOverride All
    Require all granted
</Directory>
```

### For Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location /api {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 3. Verify File Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

## 4. Check Environment File
Ensure `.env` has:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://booking-core1.jetlougetravels-ph.com
```

## 5. Test URLs
- Test: https://booking-core1.jetlougetravels-ph.com/api-debug.php
- Test: https://booking-core1.jetlougetravels-ph.com/api/bookings/index.php
- Laravel: https://booking-core1.jetlougetravels-ph.com/api/bookings/

## 6. CyberPanel Specific
In CyberPanel:
1. Go to Websites → Manage
2. Select your domain
3. Click "Rewrite Rules"
4. Ensure Laravel rewrite rules are active
