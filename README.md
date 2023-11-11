# SmartPoints 2.0

## Getting started

1. Clone the repository
2. Install JS dependencies: `npm install`
3. Install PHP dependencies: `composer install`
4. Copy `.env.example` to `.env` and fill in the values
5. Don't forget to `php artisan key:generate`
6. Migrate the database: `php artisan migrate`
7. Start the CurrApp3 API by running this inside the currapp directory: `php artisan serve --port=8080` (different port from this app)
8. Start this apps front-end compiler: `npm run dev`
9. Start this apps back-end server: `php artisan serve`
10. Go to `localhost:8000` in your browser
