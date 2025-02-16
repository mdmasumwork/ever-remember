## Ever Remember
Ever Remember is a tool to help users generate personalized memorial content.

## Quick Setup
- Clone the repository: `git clone https://github.com/your-repo/ever-remember.git`
- Configure `.env` and Apache's web root to point to the `/public` directory.

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
