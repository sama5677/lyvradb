# Workout Plans API Documentation

## Overview
RESTful APIs to serve workout plan data from JSON files without using a database. The exercise data is stored as JSON files in `storage/app/exercises/`.

---

## Project Structure

```
LYVRA-backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ExerciseController.php
│   └── Services/
│       └── ExerciseService.php
├── storage/
│   └── app/
│       └── exercises/
│           ├── Arnold Split.json
│           ├── Bro Split (Body Part Split).json
│           ├── Full Body Split.json
│           ├── Push  Pull  Legs (PPL).json
│           └── Upper Lower Split.json
└── routes/
    └── api.php
```

---

## API Endpoints

### 1. Get All Categories
**Endpoint:** `GET /api/categories`

**Description:** Returns a list of all available workout plan categories (names only).

**Response (200 OK):**
```json
[
  "Arnold Split",
  "Bro Split (Body Part Split)",
  "Full Body Split",
  "Push  Pull  Legs (PPL)",
  "Upper / Lower Split"
]
```

**cURL Example:**
```bash
curl -X GET "http://localhost:8000/api/categories"
```

---

### 2. Get Single Category with Full Details
**Endpoint:** `GET /api/categories/{plan_name}`

**Description:** Returns complete workout plan details including all days and exercises.

**Parameters:**
- `plan_name` (string, required): The name of the workout plan
  - Supports URL encoding (spaces as %20)
  - Supports slug format (hyphens instead of spaces)
  - Case-insensitive

**Examples:**
- `GET /api/categories/Arnold%20Split`
- `GET /api/categories/arnold-split`
- `GET /api/categories/arnold split`

**Response (200 OK):**
```json
{
  "plan_name": "Arnold Split",
  "days": {
    "chest & back": {
      "exercises": [
        {
          "id": 1,
          "name": "Bench Press",
          "video_url": "https://youtu.be/tuwHzzPdaGc?si=v_GEiHVJ7EdXtDmi",
          "sets": 3,
          "reps": 10,
          "rest_minutes": 2
        },
        {
          "id": 2,
          "name": "Incline Bench Press",
          "video_url": "https://youtu.be/uIzbJX5EVIY?si=XnGT7Y-0yC-ytjp5",
          "sets": 3,
          "reps": 10,
          "rest_minutes": 2
        }
      ],
      "cardio": null
    },
    "shoulders & arms": {
      "exercises": [
        {
          "id": 8,
          "name": "Barbell Clean and Press",
          "video_url": "https://youtu.be/OA_CzaDEvSM?si=t7oEMR3YXqRgGH0_",
          "sets": 3,
          "reps": 10,
          "rest_minutes": 2
        }
      ],
      "cardio": null
    }
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "message": "Workout plan not found"
}
```

**cURL Example:**
```bash
curl -X GET "http://localhost:8000/api/categories/Arnold%20Split"
```

---

### 3. Get All Exercises from a Category
**Endpoint:** `GET /api/categories/{plan_name}/exercises`

**Description:** Returns a flat list of all exercises from a specific workout plan, including the day they belong to.

**Parameters:**
- `plan_name` (string, required): The name of the workout plan

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "name": "Bench Press",
    "video_url": "https://youtu.be/tuwHzzPdaGc?si=v_GEiHVJ7EdXtDmi",
    "sets": 3,
    "reps": 10,
    "rest_minutes": 2,
    "day": "chest & back"
  },
  {
    "id": 2,
    "name": "Incline Bench Press",
    "video_url": "https://youtu.be/uIzbJX5EVIY?si=XnGT7Y-0yC-ytjp5",
    "sets": 3,
    "reps": 10,
    "rest_minutes": 2,
    "day": "chest & back"
  },
  {
    "id": 8,
    "name": "Barbell Clean and Press",
    "video_url": "https://youtu.be/OA_CzaDEvSM?si=t7oEMR3YXqRgGH0_",
    "sets": 3,
    "reps": 10,
    "rest_minutes": 2,
    "day": "shoulders & arms"
  }
]
```

**cURL Example:**
```bash
curl -X GET "http://localhost:8000/api/categories/Arnold%20Split/exercises"
```

---

### 4. Get Specific Exercise by ID
**Endpoint:** `GET /api/categories/{plan_name}/exercises/{id}`

**Description:** Returns details of a specific exercise from a workout plan.

**Parameters:**
- `plan_name` (string, required): The name of the workout plan
- `id` (integer, required): The exercise ID

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "Bench Press",
  "video_url": "https://youtu.be/tuwHzzPdaGc?si=v_GEiHVJ7EdXtDmi",
  "sets": 3,
  "reps": 10,
  "rest_minutes": 2,
  "day": "chest & back"
}
```

**Error Response (404 Not Found - Plan doesn't exist):**
```json
{
  "message": "Workout plan not found"
}
```

**Error Response (404 Not Found - Exercise doesn't exist):**
```json
{
  "message": "Exercise not found in this workout plan"
}
```

**cURL Example:**
```bash
curl -X GET "http://localhost:8000/api/categories/Arnold%20Split/exercises/1"
```

---

## Code Implementation

### ExerciseService (app/Services/ExerciseService.php)

The service class handles all file operations and business logic:

**Key Methods:**
- `getAllCategories()` - Returns list of all available categories
- `getCategory($planName)` - Returns full plan data
- `getExercise($planName, $exerciseId)` - Returns specific exercise
- `getAllExercises($planName)` - Returns all exercises from a plan
- `resolveFilename($input)` - Converts slugs to actual filenames (handles case-insensitivity)
- `categoryExists($planName)` - Checks if category exists

**Features:**
- Handles URL encoding and slug conversion
- Case-insensitive matching
- Graceful error handling
- File existence validation

---

### ExerciseController (app/Http/Controllers/ExerciseController.php)

The controller handles HTTP requests and responses:

**Key Methods:**
- `getCategories()` - GET /api/categories
- `getCategory($planName)` - GET /api/categories/{plan_name}
- `getAllExercises($planName)` - GET /api/categories/{plan_name}/exercises
- `getExercise($planName, $id)` - GET /api/categories/{plan_name}/exercises/{id}

**Features:**
- JSON responses
- Proper HTTP status codes (200, 404)
- Error message handling
- Input validation and resolution

---

## API Routes (routes/api.php)

```php
// Public Exercise/Workout Plans Routes
Route::get('/categories', [ExerciseController::class, 'getCategories']);
Route::get('/categories/{plan_name}', [ExerciseController::class, 'getCategory']);
Route::get('/categories/{plan_name}/exercises', [ExerciseController::class, 'getAllExercises']);
Route::get('/categories/{plan_name}/exercises/{id}', [ExerciseController::class, 'getExercise']);
```

---

## Error Handling

### Error Responses

**404 - Workout Plan Not Found**
```json
{
  "message": "Workout plan not found"
}
```

**404 - Exercise Not Found**
```json
{
  "message": "Exercise not found in this workout plan"
}
```

---

## Testing the APIs

### Using Postman

1. **Get All Categories**
   - Method: GET
   - URL: `http://localhost:8000/api/categories`

2. **Get Arnold Split Details**
   - Method: GET
   - URL: `http://localhost:8000/api/categories/Arnold%20Split`

3. **Get All Exercises from Upper Lower Split**
   - Method: GET
   - URL: `http://localhost:8000/api/categories/Upper%20%2F%20Lower%20Split/exercises`

4. **Get Exercise ID 1 from Arnold Split**
   - Method: GET
   - URL: `http://localhost:8000/api/categories/Arnold%20Split/exercises/1`

---

## Available Workout Plans

1. **Arnold Split** - File: `Arnold Split.json`
   - Chest & Back
   - Shoulders & Arms
   - Legs & Lower Back

2. **Bro Split (Body Part Split)** - File: `Bro Split (Body Part Split).json`
   - Chest
   - Back
   - Shoulders
   - Legs
   - Arms

3. **Full Body Split** - File: `Full Body Split.json`
   - Full Body

4. **Push Pull Legs (PPL)** - File: `Push  Pull  Legs (PPL).json`
   - Push
   - Pull
   - Leg

5. **Upper / Lower Split** - File: `Upper Lower Split.json`
   - Upper
   - Lower

---

## Performance Considerations

### Current Implementation
- Reads JSON files on each request
- Simple and straightforward

### Optional Optimization - Caching
To improve performance, you can add caching:

```php
public function getAllCategories(): array
{
    return Cache::remember('exercise_categories', 60 * 60 * 24, function () {
        // existing code
    });
}
```

**Cache Duration:** 24 hours (can be adjusted)

---

## File Structure

```
storage/
└── app/
    └── exercises/
        ├── Arnold Split.json
        ├── Bro Split (Body Part Split).json
        ├── Full Body Split.json
        ├── Push  Pull  Legs (PPL).json
        └── Upper Lower Split.json
```

Each JSON file contains:
- `plan_name` - Name of the workout plan
- `days` - Object containing days as keys
  - Each day contains:
    - `exercises` - Array of exercise objects
    - `cardio` - Optional cardio information

---

## Notes

1. **Slug Support**: The API automatically converts hyphens to spaces
   - `arnold-split` → `Arnold Split`
   - `upper-lower-split` → `Upper / Lower Split`

2. **Case Insensitivity**: Category names are matched case-insensitively
   - `arnold split` = `Arnold Split` = `ARNOLD SPLIT`

3. **URL Encoding**: Spaces in URLs should be encoded as `%20`
   - Correct: `/api/categories/Arnold%20Split`
   - Correct: `/api/categories/arnold-split`

4. **JSON File Location**: All JSON files must be stored in `storage/app/exercises/`

5. **Storage Facade**: The implementation uses Laravel's Storage facade with the 'local' disk by default

---

## Troubleshooting

**404 Errors:**
- Check that the plan name is correct
- Ensure URL encoding for spaces (%20)
- Verify JSON files exist in `storage/app/exercises/`

**File Not Found:**
- Verify `storage/app/exercises/` directory exists
- Check file permissions
- Ensure JSON files are in the correct location

**Invalid JSON:**
- Check JSON syntax in exercise files
- Use a JSON validator if needed

---

## Future Enhancements

1. Add caching layer
2. Add filtering capabilities (by difficulty, duration, etc.)
3. Add search functionality
4. Add statistics (total exercises per plan, etc.)
5. Add database persistence
6. Add admin endpoints to manage plans
7. Add export functionality (CSV, PDF)
8. Add progress tracking per user

