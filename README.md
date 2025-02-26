## Ever Remember
Ever Remember is a tool to help users generate personalized memorial content.

## Quick Setup
- Clone the repository: `git clone https://github.com/your-repo/ever-remember.git`
- Create database and create the table with the sql in `/database/migrations/create_payments_table.sql` file.
- Configure `.env` in root direcotyr, and set all the variables properly.
- Configure Apache's web root to point to the `/public` directory. If it is live server, then we have the right `.htaccess.production`, `.htaccess.dev` and `.htaccess.staging` inside root directory. We have to just put the the righ file in the root directory, naming it just `.htaccess`. When we do github actions auto deploy, it automatically copies the right file.

## Features
- Generate memorial content with privacy and care.
- Modular structure for scalability.
- Secure practices for data handling.

## Installation
- Install redis-server on the server. Redis server is required for Rate limiting.

```
# For Ubuntu/Debian
# Install redis-server
sudo apt-get update
sudo apt-get install redis-server

# Install php redis extension
sudo apt-get install php-redis

# Enable extension in php.ini
extension=redis.so

# Restart redis server
sudo systemctl start redis
sudo systemctl enable redis-server
```

