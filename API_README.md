
# MyClimate API

## Table of contents
1. [Author](#1-author)
2. [About MyClimate project](#2-about-myclimate-api-project)
3. [Built with](#3-built-with)
4. [UML class diagram](#4-uml-class-diagram)
5. [API documentation](#5-api-docs)
6. [Project structure](#6-project-structure)
7. [How to run the service](#7-how-to-run-the-api)
8. [How to execute the tests](#8-how-to-execute-the-tests)
9. [Hours spent](#9-hours-spent)
10. [References](#10-references)

## 1. Author
Marc Vivas Baiges

## 2. About MyClimate API project
This system allows users to remote managing information related with
climate installation homes, sensors and users within a web services system.

## 3. Built with
The project is built with **Laravel 9** which is a php framework.

## 4. UML Class Diagram
![UML Class Diagram](api_uml_diagram.png)

## 5. API Docs
The API endpoints are documented in a file named `api_doc.yaml`. In order to preview 
the API endpoints in a friendly UI and interact with it, 
open [Swagger Editor](https://editor.swagger.io/) and import the file.  

All endpoints require an authentication token that can only be obtained by logging in or registering a user.  
This token must be provided in the header of each request. Example:
```
Authorization: Bearer 84423094230h3242304230423hk4jh23
```

## 6. Project structure
In case you have never seen a Laravel project, it can be quite hard to 
find the code that really matters. <br>
For this reason, the project structure it's now going to be explained.

### Migrations
Database migrations are located at `./MyClimateAPI/database/migrations`.
  
- Here you will be able to see which are the tables created and their attributes.

### Routes
Routes are located at `./MyClimateAPI/routes/api`.

- Here is where the endpoints paths are defined.  
- Each route executes a function when it is called. The function is usually a method from a Controller.

### Controllers
Controllers are located at `./MyClimateAPI/app/Http/Controllers`. 
- Define the logic of all the endpoints of the application.

### Services
Services are located at `./MyClimateAPI/app/Services`.
- Controllers use services to interact with the database. 
- Good practice.

### Resources
Resources are located at `./MyClimateAPI/app/Http/Resources`.
- Resources are used to transform a database model to JSON format.

### Requests
Requests are located at `./MyClimateAPI/app/Http/Requests`.
- Requests are used to check if the body of a request is correct.

### Models
Models are located at `./MyClimateAPI/app/Models`.
- Each database table has a corresponding `Model` that is used to interact with that table. 

### Tests
Tests are located at `./MyClimateAPI/tests/Feature`. 

## 7. How to run the API
You should have installed `docker compose` in your system.

To run only the API you have to insert the following command:

```bash
sudo docker compose up MyClimateAPI
```
Once is running, you can send requests to localhost:8000 to interact with the API. 
I recommend to use Postman or Swagger to test the API.

## 8. How to execute the tests
A total of 65 tests have also been written. To execute them, the container of the API must be up and running.

Once is running, open another terminal where `docker-compose.yml` is located, and insert the following command.
```bash
sudo docker compose exec MyClimateAPI php artisan test
```

## 9. Hours spent
- December 2 2022: 16:30 - 19:30 Project planning ->  <strong> 3 hours </strong>  
- December 3 2022: 9:00 - 17:30 Project development -> <strong> 8.5 hours </strong> 
- December 4 2022: 8:00 - 15:00 Project development -> <strong> 7 hours </strong>
- December 5 2022: 16:30 - 19:00 Project development -> <strong> 2.5 hours </strong>
- December 6 2022: 7:00 - 11:30 Project development -> <strong> 4.5 hours </strong>
- December 7 2022: 16:00 - 18:00 Project development (Finished all endpoints) -> <strong> 2 hours </strong>
- December 8 2022: 9:00 - 14:00 Include project 1 -> <strong> 5 hours </strong>
- December 9 - 11 2022:  Documentation -> <strong> 8.5 hours </strong>
- <strong>  Total:   41 hours  </strong> 

## 10. References
1. API token authentication: https://laravel.com/docs/9.x/sanctum#api-token-authentication
