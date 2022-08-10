# Project informations
This project is the sixth project of the online course on OpenClassrooms : [Développeur d'application - PHP / Symfony](https://openclassrooms.com/fr/paths/59-developpeur-dapplication-php-symfony)

## Description of needs
Develop a community website called SnowTricks which lists all the snowboard tricks added by the user of the application.

List of pages needed :
- home page displaying a list of the tricks
- add a new trick page
- edit a trick page
- details page of a trick with a comment section

## Installation

### Cloning the project
```
git clone https://github.com/GN4RK/P6
```

### Installing dependencies 
```
composer install
npm install
npm run dev
```

### Configurations

#### Database
Change database connection in .env file : 
```
DATABASE_URL="mysql://root:@localhost/database_name?serverVersion=your_server&charset=utf8"
```

#### Mailer system
Change database connection in .env file : 
```
MAILER_DSN=gmail://your.address@gmail.com:******************@default
```

#### php.ini
```
post_max_size = 40M
upload_max_filesize = 40M
```

### Database migration
Run database migration on the new environnement
```
php bin/console doctrine:migrations:migrate
```

### Load data fixture
This command will load fresh data into your database
```
php bin/console doctrine:fixtures:load
```

### Running server
```
symfony server:start
```

## Badge Codacy
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c64f6314c2c14d4aa61c3692ab45182e)](https://www.codacy.com/gh/GN4RK/P6/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=GN4RK/P6&amp;utm_campaign=Badge_Grade)