# Mini Order Management API

Build a small backend system where users can create products and place orders using REST APIs.

## Installation

First clone this repository, install the dependencies, and setup your .env file.

```
git clone https://github.com/yogesh-vakhre/Mini-Order-Management-API.git
cd Mini-Order-Management-API/
composer install
cp .env.example .env
```

Setup database configuration in .env file on root directory.

```
DB_DATABASE=mini_order_management
DB_USERNAME=root
DB_PASSWORD=
```

Setup email configuration in .env file on root directory.

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=mygoogle@gmail.com
MAIL_PASSWORD=rrnnucvnqlbsl
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=mygoogle@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
Setup Queue configuration in .env file on root directory.
```

```
QUEUE_CONNECTION=database
```

And run the initial run your application.

```
php artisan key:generate
php artisan migrate --seed
php artisan cache:clear
php artisan config:clear
php artisan queue:work
```

And run the your laravel application.

```
php artisan serve
```

Open bewlow your localhost server in broweser

```
http://127.0.0.1:8000/
```

Now you can login with following credential:

```
Email: admin@gmail.com
Password: admin@123
```

if you face permissioan realated issue in the your laravel application on Ubuntu or Centos oprating systeme then do this.

```
sudo chown -R deployer:www-data /var/www/html/Mini-Order-Management-API/;
find /var/www/html/Mini-Order-Management-API -type f -exec chmod 664 {} \;
find /var/www/html/Mini-Order-Management-API -type d -exec chmod 775 {} \;
chgrp -R www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

## Setting Up Redis

### Install Redis

On Linux/Mac: sudo apt install redis-server
On Windows: Use Docker or WSL.
Verify installation: redis-cli ping → should return PONG.

Configure Laravel to Use Redis
Laravel automatically uses Redis when CACHE_DRIVER=redis

```
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```
