
# SpotPrint

Laravel Framework 11.9  
PHP 8.3  



## Access info
 

```
admin@spotprint.com
Pass: admin123
```


# User Guide




## Local Installation

http://localhost:8000  

Follow the steps mentioned below to install and run the project.

1. Clone or download the repository
2. Go to the project directory and run `composer install --optimize-autoloader --no-dev`
3. Create `.env` file by copying the `.env.local.backup`.
4. Update the database name and credentials in `.env` file
5. Run mysql schema file or migration to load database on server: `php artisan migrate:fresh --seed`
6. We are using CDN. there is no need to run link storage directory: `php artisan storage:link`
7. You may create a virtual host entry to access the application or run `php artisan serve` from the project root and visit `http://127.0.0.1:8000`
8. Start Reverb server in separate terminal `php artisan reverb:start --debug`



## Server Installation



Follow the steps mentioned below to install and run the project.

1. Clone or download the repository
2. Go to the project directory and run `composer install --optimize-autoloader --no-dev`
3. Create `.env` file by copying the `.env.dev.server`.
4. Update the database name and credentials in `.env` file
5. Run mysql schema file or migration to load database on server: `php artisan migrate:fresh --seed`
6. We are using CDN. there is no need to run link storage directory: `php artisan storage:link`
7. Run socket workers with supervisor. supervisor config files are saved in DOCs folder. `websockets`
8. Setup cron job `/usr/bin/php8.3 /home/htdocs/artisan schedule:run`  


Internal websocket is being used on port 6002 with Supervisor. after every change related to Socket code and DB, Restart Reverb on server   
`supervisorctl restart all`  




## Socket Setup  

php artisan install:api  

// on client side   
npm install laravel-echo pusher-js --legacy-peer-deps


## Custom Commands  

