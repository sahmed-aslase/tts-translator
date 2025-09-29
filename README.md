# Multilingual Translator (Laravel)

## About the Project
A simple multilingual translator built with Laravel. Enter English text, select one or more of Spanish, French, or German, and view the translated results. The app focuses on simplicity, clean UI, and no external API keys.

### Features
- English → Spanish (es), French (fr), German (de)
- Translate to multiple target languages at once
- History of translations stored in MySQL with pagination
- Clean Tailwind UI (via CDN)
- Loading overlay and disabled controls during actions
# Multilingual Translator (Laravel)

## About the Project

A simple multilingual translator built with Laravel. Enter English text, select one or more of Spanish, French, or German, and view the translated results. The app focuses on simplicity, a clean UI, and no external API keys.

### Features

- English → Spanish (es), French (fr), German (de)
- Translate to multiple target languages at once
- Translation history stored in MySQL with pagination
- Clean Tailwind UI (via CDN)
- Loading overlay and disabled controls during actions
- No API keys required

### Tech Stack

- PHP 8.4 (Laravel)
- MySQL 8
- Tailwind CSS (via CDN)

---

## Setup Guide

### 1) Create a database

Create a MySQL database (example):

```
tts_translator
```

### 2) Environment

Copy the example env and update DB values (or set the following in your `.env`):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tts_translator
DB_USERNAME=root
DB_PASSWORD=
```

### 3) Migrate

From the project root run:

```
php artisan migrate
```

### 4) Run the app

- Using the built-in server:

```
php artisan serve
```

  Then open the shown URL (for example http://127.0.0.1:8000).

- Or configure a local virtual host (example: http://tts-translator.test).

### Endpoints

- GET / → UI
- POST /translate → Translate request

Request (JSON):

```json
{
  "text": "Your English sentence",
  "targets": ["es", "fr", "de"]
}
```

Response (JSON):

```json
{
  "items": [
    { "id": 1, "target": "es", "translated": "..." },
    { "id": 2, "target": "fr", "translated": "..." }
  ]
}
```

---

## Build Time and Steps (approx. 1 hour)

- Scaffold project, DB defaults, migration, routes
- Backend: model, controller, translation service; strict validation for targets (es, fr, de)
- Frontend: Blade view with Tailwind UI, multi-language selection, results rendering
- UX polish: loading overlay, disable/enable controls, history with pagination

---
