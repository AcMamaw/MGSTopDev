<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## Deployment Documentation 

Cloud Deployment Guide 
 
This document describes the deployment architecture and step-by-step 
process for deploying the MGS Laravel POS & Inventory System to AWS 
Cloud. 
 
 
Architecture Overview 
 
Application: Laravel 11 + Blade + Alpine.js (full-stack PHP) 
 
Web Server: Nginx (reverse proxy + static file server) 
 
PHP Runtime: PHP 8.2-FPM (FastCGI process manager) 
 
Database: AWS RDS MariaDB 
 
File Storage: Amazon S3 (PDF reports) 
 
Image CDN: Cloudinary (product images) 
 
Email: AWS SES (transactional emails) 
 
OS: Ubuntu 24.04 LTS on AWS EC2 
 
 
 
System Architecture 
 
 
User (Browser) 
      ↓ 
EC2 Instance (Nginx + PHP-FPM) 
      ↓ 
AWS RDS (Database) 
      ↓ 
AWS S3 (PDF Files) 
      ↓ 
Cloudinary (Images) 
 
 
 
 1. Launch EC2 Instance 
 
Go to AWS Console → EC2 → Launch Instance 
  
       -     Choose Ubuntu 24.04 LTS 
 
       -     Instance type: t3.micro (Free Tier) 
 
       -     Create or select key pair (example: mgs-key.pem) 
 
       -     Allow HTTP and SSH access 
 
2. Configure Security Groups (Firewall) 
 
Go to EC2 → Security Groups → Edit Inbound Rules 
 
TYPE   |   PORT    |   SOURCE 
SSH    |    22     |    MY IP 
HTTP   |    80     |  0.0.0.0/0 
HTTPS  |    443    |  0.0.0.0/0 
 
 
 
3. Connect to EC2 via SSH 
 
Option A: From Windows Terminal 
 
Bash: 
 - ssh -i mgs-key.pem ubuntu@3.106.132.231 - http://3.106.132.231 
 
 
Option B: From AWS CloudShell (Recommended) 
 
1. Open CloudShell from AWS Console 
2. Click Actions → Upload file → upload mgs-key.pem 
3. Run: 
 
Bash: 
 
       -     chmod 400 mgs-key.pem 
       -     ssh -i mgs-key.pem ubuntu@3.106.132.231 
 
 
 
 
4. Install Required Software 
 
Step 1: Update System 
 
Bash: 
 - sudo apt update && sudo apt upgrade -y 
 
 
Step 2: Install PHP 8.2 and Extensions 
 
Bash: 
 - sudo apt install -y software-properties-common - sudo add-apt-repository ppa:ondrej/php -y - sudo apt update - sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \ - php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath 
 
 
Step 3: Install Nginx 
 
Bash: 
 - sudo apt install -y nginx 
 
 
Step 4: Install Composer 
 
Bash: 
 - curl -sS https://getcomposer.org/installer | php - sudo mv composer.phar /usr/local/bin/composer 
 
 
Step 5: Install Git 
 
Bash: 
 - sudo apt install -y git 
 
 
Verify Installation 
 
Bash: - php -v - nginx -v - composer --version - git --version 
 
 

 
5. Clone and Configure Laravel App 
 
Step 1: Clone Repository 
 
Bash: 
 - cd /var/www - sudo git clone https://github.com/AcMamaw/MGSTopDev.git mgs - sudo chown -R ubuntu:ubuntu /var/www/mgs - cd /var/www/mgs 
 
Step 2: Install Dependencies 
Bash: - 
composer install --no-dev --optimize-autoloader 
Step 3: Configure Environment 
Bash: - - - 
cp .env.example .env 
php artisan key:generate 
nano .env 
Key .ENV values to set: 
APP_NAME=Laravel 
APP_ENV=production 
APP_KEY=base64:vPm8a2Quxtw15R+ij+5cFTchuASlm5A2o/H5VxjbBS8= 
APP_DEBUG=false 
APP_URL=http://3.106.132.231 
DB_CONNECTION=mysql 
DB_HOST=mgstopdev-db2.cdqqyk0q8x8z.ap-southeast-2.rds.amazonaws.com 
DB_PORT=3306 
DB_DATABASE=mgsdb 
DB_USERNAME=admin 
DB_PASSWORD=MGSAdmin2025 
SESSION_DRIVER=cookie 
SESSION_LIFETIME=120 
AWS_ACCESS_KEY_ID=AKIAZBTFUPTENKO5FCGV 
AWS_SECRET_ACCESS_KEY=IL8zcmU/ymfhNcd9jVyKt1JuUXxkpjbweu/Xu
mLZ 
AWS_DEFAULT_REGION=ap-southeast-2 
AWS_BUCKET=mgstopdev-reports 
AWS_USE_PATH_STYLE_ENDPOINT=false 
CLOUDINARY_CLOUD_NAME=dlhcczwfz 
CLOUDINARY_API_KEY=896181671383421 
CLOUDINARY_API_SECRET=H2xrDLLGgPTU8Tr6HjGQBrMu5yY 
CLOUDINARY_URL=cloudinary://896181671383421:H2xrDLLGgPTU8Tr6HjG
QBrMu5yY@dlhcczwfz 
FILESYSTEM_DISK=s3 
QUEUE_CONNECTION=database 
CACHE_STORE=database 



6. Set Permissions and Run Migrations 
Step 1: Set File Permissions 
Bash: - - - - 
sudo chown -R www-data:www-data /var/www/mgs/storage 
sudo chown -R www-data:www-data /var/www/mgs/bootstrap/cache 
sudo chmod -R 777 /var/www/mgs/storage 
sudo chmod -R 777 /var/www/mgs/bootstrap/cache 
Step 2: Create Storage Symlink 
Bash: - 
php artisan storage:link 
Step 3: Run Database Migrations 
Bash: - 
php artisan migrate --force 
Step 4: Cache Configuration for Production 
Bash: - - - 
php artisan config:cache 
php artisan route:cache 
php artisan view:cache 



7. Configure Nginx Web Server 
Step 1: Create Nginx Config 
Bash: - 
sudo nano /etc/nginx/sites-available/mgs 
 
Paste this configuration: 
 
Code: 
    server { 
        listen 80; 
        server_name 3.106.132.231; 
        root /var/www/mgs/public; 
        index index.php index.html; 
        location / { 
            try_files $uri $uri/ /index.php?$query_string; 
        } 
         location ~ \.php$ { 
            include snippets/fastcgi-php.conf; 
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; 
            fastcgi_paramSCRIPT_FILENAME$realpath_root$fastcgi_script_name; 
            include fastcgi_params; 
        } 
        location ~ /\.ht { deny all; } 
        client_max_body_size 10M; 
     } 
 
 
Step 2: Enable Site and Restart Nginx 
 
Bash: 
 - sudo ln -s /etc/nginx/sites-available/mgs /etc/nginx/sites-enabled/ - sudo rm /etc/nginx/sites-enabled/default - sudo nginx -t - sudo systemctl restart nginx - sudo systemctl restart php8.2-fpm 
 
 
Now: http://3.106.132.231 → Nginx → Laravel App (MGS POS System) 
 
 
 
8. Configure Amazon S3 for File Storage 
 
Step 1: Create S3 Bucket 
 
●  Go to AWS S3 Console 
●  Create bucket (e.g., mgstopdev-reports) 
●  Set region to ap-southeast-2 
●  Keep Block Public Access enabled (private bucket) 
 
Step 2: Create IAM User with S3 Policy 
 
1. Go to IAM → Users → Create User 
 
2. Attach the following policy: 
Code: 
   { 
  "Version": "2012-10-17", 
  "Statement": [{ 
    "Effect": "Allow", 
    "Action": [ 
      "s3:PutObject", 
      "s3:GetObject", 
      "s3:DeleteObject" 
    ], 
    "Resource": "arn:aws:s3:::mgstopdev-reports/*" 
  }] 
} 
 
3. Download Access Key ID and Secret Access Key 
 
4. Add them to your .env file 
 
 
Step 3: Laravel S3 Configuration 
 
In config/filesystems.php the s3 driver is already configured. 
Set FILESYSTEM_DISK=s3 in .env to use S3 as default storage. 
 
 

9. Deploying Updates 
 
After pushing changes to GitHub, SSH into EC2 and run: 
 
Bash: 
 - cd /var/www/mgs - git pull origin main - composer install --no-dev --optimize-autoloader - php artisan migrate --force - php artisan config:cache - php artisan route:cache - php artisan view:cache - sudo chown -R www-data:www-data storage bootstrap/cache 
 
 
This ensures the latest code is deployed without downtime. 

 
Deployment Summary 
 
 
SERVICE       |      STATUS        |         DETAILS 
EC2 Instance  |       Live         | Ubuntu 24.04, t3.micro 
Nginx         |      Running       |         Port 80              
PHP-FPM       |      Running       |         PHP 8.2 
RDS           |     Connected      |         MariaDB 
S3            |     Connected      |    PDF report storage 
Cloudinary    |     Connected      |    Product image CDN 
SES           |     Connected      |      Email sending 


 
Live URL: http://3.106.132.231 
GitHub Repository: https://github.com/AcMamaw/MGSTopDev