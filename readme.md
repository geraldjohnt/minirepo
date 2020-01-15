# Live Video/Chat Prototype

Using Laravel 5.2.* doing RESTful API and soon to have a extension for WebRTC using Nodejs.



## Dev Environment Requirements  
Install Composer https://getcomposer.org/download/  

Install Docker https://docs.docker.com/engine/installation/  

Install NPM with Node.js https://www.npmjs.com/get-npm

## App Installation setup
##### Clone the repository 
##### Create the .env file (copy the .env.example and update it)
`cp .env.example .env`

##### Change APP_URL of .env to your ip address

`APP_URL=https://myipaddress`

##### Go to project's root directory and run the docker configuration:

`docker-compose up -d --build` for rebuilding containers

##### Make sure the docker containers are running properly

`docker-compose ps`

##### create your certificate and key .pem files

Go to certs directory then run the command below

Just replace your 'YOUR-IP' to your actual ip address

For mac and linux:
`openssl req -subj '/CN=YOUR-IP' -x509 -newkey rsa:4096 -nodes -keyout key.pem -out cert.pem -days 365;`

For windows:
`openssl req -subj '//CN=YOUR-IP' -x509 -newkey rsa:4096 -nodes -keyout key.pem -out cert.pem -days 365;`



## Install the Mee2box project vendor files and node packages

##### Install Laravel Dependencies

`docker-compose run app composer install`

##### Install the project node modules

##### If You have node installed in your computer
Just Install it directly `npm install`

##### But if you don't have node installed in your computer 

try this `docker-compose run app npm install`


##### If error occured when installing npm due to permission denied

try `docker-compose run app npm install --unsafe-perm`

##### Migrate Database data using 

`docker-compose run app php artisan migrate --seed`

##### Build the webpack bundler with npm

`docker-compose run app npm run dev`

##### The Docker virtual machine usually run in `https://youripaddress`


## &nbsp;
<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
