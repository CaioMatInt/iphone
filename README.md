## Setup
Clone this repository

Run composer install

Update your env with your db password/key

## To run php unit tests: 
php artisan migrate:fresh

Not necessary to run php artisan db:seed [1]

Execute php artisan test

Obs:
[1] It's not needed to run db:seed since I'm using factories, but if youre running it:

Please update this temp var to false:

DatabaseSeeder -> SHOULD_ADD_TESTING_DATA = false;

## To test the endpoint:
Run php artisan migrate --seed to run the seeds with fake data.

Check one user_id in your DB (You can use 1, 2 etc.).

Make a GET request to: http://127.0.0.1:8000/users/{user_id}/achievements

## About This Test

This repository has been made following the following test description: https://ipsmedia.notion.site/ipsmedia/Back-end-Developer-Test-26cb7ae808204668a6ca3c408eaa6d4f

## Missing (if I had more time)

- Would implement Cache to avoid making the same querie a bunch of times (Example: loading the "Beginner" badge row).
- Would organize the tests better using Traits, and also implement more tests.
- Would check if I could get a better performance on the User Achievements endpoints by making just one DB::select call.
