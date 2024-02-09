# Introduction
To build rest API for storing and scraping the data from the url which can be list of URLs and selectors.

## Project Setup
1. git clone https://github.com/mvhora-arsenal/reiz.tech-test.git
2. Goto project directory and execute the following command
3. Copy .env.example to .env file
4. Copy the redis password which was set earlier

## Postman Collection
```bash
#Here I am attaching file to import JSON in Postman to verify the scrapper
/postman_collection.json
```

```bash
$ cp .env.example .env
$ composer install
$ docker-compose up -d
$ php artisan queue:work
# After follow these steps you can import postman to get the API working
```

# Routes

### POST /api/jobs
```
It will create job details and fetch data
```

```bash
#Sample Data
{
    "urls" : ["https://www.worldometers.info/coronavirus/","https://laravel.com/docs/10.x/http-client"], 
    "selectors" : [".maincounter-number", "#main-content h1", "h2","a","style"]
}
```

### GET /api/jobs
```
It will retrieve all data available from redis
```

### GET /api/jobs/{id}
``` 
It will get a job from redis by ID 
```

### DELETE /api/jobs/{id}
```
It will delete a job from redis by id
```
