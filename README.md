REST API Symfony2

This is the code I implemented base on the article at [Symfony2 API REST the best way](http://welcometothebundle.com/symfony2-rest-api-the-best-2013-way/)

### Install with Composer

As Symfony uses [Composer][1] to manage its dependencies, the recommended way
to create a new project is to use it.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -s http://getcomposer.org/installer | php

Then, clone the source code from my repository

    https://github.com/roykiet/symfony2_rest.git
    
Go to project folder
    
    cd symfony2_rest

Next, Install depenencies

    php composer.phar update

While composer download and update dependencies, the tool will require us configure database and e-mail. Please, set the configuration match with your machine.

### Run the command.

To create database, doctrine will create a database by your configure
    
    php app/console doctrine:database:create 

To create table from entities which you created in source code.
    
    php app/console doctrine:schema:create

Run unit test

    bin/phpunit -c app 
    
View api for calling

    app/console route:debug | grep api

[1]:  http://getcomposer.org/
