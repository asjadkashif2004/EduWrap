# EduWrap

EduWrap is a full-stack AI-powered learning platform with:

- React Native (Expo) mobile app
- Laravel REST API backend
- MySQL (XAMPP) relational schema
- Secure webhook event logging and validation
- FCM-ready notification pipeline
- Recommendation and analytics APIs

## Project Structure

- `backend` - Laravel API (`Controller -> Service -> Repository -> Model`)
- `mobile` - React Native app with secure token auth

## Backend Setup (Laravel)

1. Open XAMPP and start **MySQL**.
2. Create database `eduwrap`.
3. Configure `backend/.env` (already pre-filled for local XAMPP):
   - `DB_CONNECTION=mysql`
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3307` (use your active MySQL port)
   - `DB_DATABASE=eduwrap`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=`
   - `WEBHOOK_SECRET=eduwrap-webhook-secret`
   - `FCM_SERVER_KEY=<your_fcm_server_key>`
4. Run:
   - `cd backend`
   - `php artisan migrate --seed`
   - `php artisan serve`

API base URL: `http://127.0.0.1:8000/api`

## Mobile Setup (React Native / Expo)

1. Open `mobile/src/services/api.ts`.
2. Keep `API_URL` as:
   - Android emulator: `http://10.0.2.2:8000/api`
   - Physical device: replace with your machine IP
3. Run:
   - `cd mobile`
   - `npm install`
   - `npm run android` (or `npm run web`)

## UI/UX Improvements Included

- Modern card-based design with richer spacing, hierarchy, and shadows
- Dynamic dashboard with progress widgets and quick action shortcuts
- Improved courses screen with search, categories/level badges, and visual loading states
- Enhanced course details page with docs + video quick actions
- Better cart interactions with clear button states and checkout progress feedback

## Required API Endpoints

- `POST /api/register`
- `POST /api/login`
- `GET /api/courses`
- `POST /api/enroll`
- `POST /api/cart`
- `POST /api/order`
- `GET /api/notifications`
- `POST /api/webhook`

## Extra APIs Implemented

- `PATCH /api/enroll/{enrollmentId}/progress`
- `GET /api/my-enrollments`
- `GET /api/cart`
- `GET /api/recommendations`
- `POST /api/analytics/track`
- `GET /api/analytics/insights`
- `GET/PATCH /api/profile`
- `PATCH /api/profile/password`

## Course Content Enrichment

- Added an expanded catalog with 10+ courses across Backend, Mobile, AI, Security, DevOps, and more
- Every seeded course includes:
  - documentation link (`documentation_url`)
  - YouTube video link (`youtube_url`)
  - thumbnail image (`thumbnail_url`)
  - level badge (`level`)
- New migration: `add_learning_resources_to_courses_table`

## Webhook Security

- Uses HMAC SHA-256 signature validation on `X-Webhook-Signature`
- Secret key sourced from `WEBHOOK_SECRET`
- Events logged in `webhook_logs`

## Core Flow

1. User enrolls or checks out from app
2. Laravel stores records in MySQL
3. Laravel dispatches webhook event
4. Webhook handler validates and logs the event
5. Notification is stored in `notifications`
6. FCM sender pushes to device (when configured)

## Checkout Troubleshooting

- Ensure backend is running (`php artisan serve`) and app points to correct API URL
- Checkout now validates:
  - cart is not empty
  - cart has purchasable course items
- Frontend checkout button now has robust loading/disabled states to prevent duplicate requests

