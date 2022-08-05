# Installation

## Clone the project

## Install dependencies 
```
composer instal
```

## Configurations

### Database
Change database connection in .env file : 
```
DATABASE_URL="mysql://root:@localhost/snow_tricks?serverVersion=mariadb-10.4.10&charset=utf8"
```

### Mailer system
Change database connection in .env file : 
```
MAILER_DSN=gmail://your.address@gmail.com:******************@default
```

### php.ini
```
post_max_size = 40M
upload_max_filesize = 40M
```

## Database migration
Run database migration on the new environnement
```
php bin/console doctrine:migrations:migrate
```

## Load data fixture
This command will load fresh data into your database
```
php bin/console doctrine:fixtures:load
```
