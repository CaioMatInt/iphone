## About This Test
- The repository was structured following the specifications provided here: [Back-end Developer Test Specifications](https://ipsmedia.notion.site/ipsmedia/Back-end-Developer-Test-26cb7ae808204668a6ca3c408eaa6d4f).
- Some classes were imported from the test instructions, hence they were not defined by the repo's owner.
  
## Setup
1. Clone the repository.
2. Execute `composer install` to install dependencies.
3. Update your `.env` file with the appropriate database credentials and keys.

## Running PHPUnit Tests
- Perform a fresh migration with `php artisan migrate:fresh` if you have previously run php artisan db:seed. In the other case, just run `php artisan migrate`.
- Run tests with `php artisan test`.

- Note: There is no need to seed the database (`php artisan db:seed`) as factories are utilized. However, should you choose to do so, set `SHOULD_ADD_TESTING_DATA` to `false` in `DatabaseSeeder`.

## Testing the Endpoint
- To populate the database with test data, run `php artisan migrate --seed`. If you have already executed the migration, simply use `php artisan db:seed`.
- Locate a `user_id` in your database (e.g., 1, 2, etc.).
- Make a GET request to: `http://127.0.0.1:8000/users/{user_id}/achievements`.

## Design Decisions
- Tables `user_lesson_progress`, `user_comment_progress`, and `user_achievement_progress` have been created to streamline the retrieval of current progress data, thereby avoiding the need for repetitive `SELECT COUNT` queries.

## Future Enhancements (Given More Time)
- Implementation of caching to reduce repetitive queries, such as loading the "Beginner" badge details multiple times.
- Improve the organization of the tests with the use of Traits, along with the addition of further test cases.
- Exploration of performance optimizations for the User Achievements endpoint, possibly by reducing to a single `DB::select` call.
