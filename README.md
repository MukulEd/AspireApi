## ALL DATA IS IN MASTER BRANCH

## Env Setup

Add DB_DATABASE name in .env for local database , or set credentials accordingly for another Db Used.
Add values according to your setup
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=aspire
    DB_USERNAME=root
    DB_PASSWORD=
    credentials accordingly.
    
## Create DB
Add Db same as written in .env to your server(local/remote)

## Add Tables
Run command- php artisan migrate set up database tables

## set up phpunit.xml

Replace the values in following lines as per .env file
<server name="DB_CONNECTION" value="mysql"/>
<server name="DB_DATABASE" value="aspire"/>

This will help you to test the application

## Add Records to User table
Run Command- php artisan db:seed 

it will put 5 random records in users table having password abc@123456 in encrypted form.

## Run Command - composer install to download the required packages.

## * Notes
All Routes are prefix with /api and can be found in api.php route file.

## To Test API
run command-php artisan test


## POSTMAN COLLECTION LINK
https://www.getpostman.com/collections/f628500aab70ee998f00







