[![Quality Status](https://sonarcloud.io/api/project_badges/measure?project=adshares-conversion&metric=alert_status)](https://sonarcloud.io/dashboard?id=adshares-conversion)

## ADS Conversion Tools

## Installation

```
cd /home/adshares
git clone https://github.com/adshares/conversion.git
cd conversion
composer install
cp .env.example .env
```

### Nginx
```
tee /etc/nginx/sites-enabled/conversion <<'ADSHARES'
server {
    listen          80;
    
    server_name     conversion.ads;
    root            /home/adshares/conversion/public;
    
    index           index.htm index.html index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.2-fpm.sock;
    }
}
ADSHARES
```
See https://lumen.laravel.com/docs/5.1#installation

## Configuration

Converter parameter with *ADS_* prefix:

| Parameter                      | Default                                    |
| ------------------------------ | :----------------------------------------- | 
| *CONTRACT_ADDRESS*             | 0x422866a8f0b032c5cf1dfbdef31a20f4509562b0 |
| *TRANSFER_METHOD*              | 0xa9059cbb                                 | 
| *TRANSFER_TOPIC*               | 0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef | 
| *BURN_ADDRESS*                 | 0x0                                        | 
| *MIN_TOKEN_AMOUNT*             | 1                                          | 
| *MIN_MASTER_NODE_TOKEN_AMOUNT* | 20000                                      | 
| *START_BLOCK*                  | 0x56BC12                                   |


See https://lumen.laravel.com/docs/5.1#environment-configuration