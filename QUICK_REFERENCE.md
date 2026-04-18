# 🏋️ Workout Plans API - Quick Reference Guide

## 🎯 Available Endpoints

### Get All Categories
```
GET /api/categories
```
Returns array of all available workout plan names.

### Get Full Plan Details
```
GET /api/categories/{plan_name}
```
Returns complete plan with all days and exercises.

### Get All Exercises from Plan
```
GET /api/categories/{plan_name}/exercises
```
Returns flat list of all exercises with their day.

### Get Specific Exercise
```
GET /api/categories/{plan_name}/exercises/{id}
```
Returns single exercise details.

---

## 💡 Usage Examples

### Example 1: Get Categories in Frontend
```javascript
// Fetch all available plans
fetch('http://localhost:8000/api/categories')
  .then(res => res.json())
  .then(categories => {
    console.log(categories);
    // Output: ["Arnold Split", "Bro Split (Body Part Split)", ...]
  });
```

### Example 2: Load Selected Plan
```javascript
// When user selects "Arnold Split"
const planName = "Arnold Split";
fetch(`http://localhost:8000/api/categories/${encodeURIComponent(planName)}`)
  .then(res => res.json())
  .then(plan => {
    console.log(plan.plan_name);
    console.log(Object.keys(plan.days)); // ["chest & back", "shoulders & arms", ...]
  });
```

### Example 3: Get Exercise Details
```javascript
// Get specific exercise
const planName = "Arnold Split";
const exerciseId = 1;
fetch(`http://localhost:8000/api/categories/${encodeURIComponent(planName)}/exercises/${exerciseId}`)
  .then(res => res.json())
  .then(exercise => {
    console.log(exercise.name);      // "Bench Press"
    console.log(exercise.sets);      // 3
    console.log(exercise.day);       // "chest & back"
  });
```

### Example 4: Display All Exercises
```javascript
// Get all exercises from a plan
const planName = "Upper / Lower Split";
fetch(`http://localhost:8000/api/categories/${encodeURIComponent(planName)}/exercises`)
  .then(res => res.json())
  .then(exercises => {
    exercises.forEach(ex => {
      console.log(`${ex.id}. ${ex.name} (${ex.day}) - ${ex.sets}x${ex.reps}`);
    });
  });
```

---

## 🔗 URL Encoding Examples

When using special characters or spaces in URLs:

```
Single Spaces:
- Original: "Arnold Split"
- Encoded: "Arnold%20Split" or use encodeURIComponent()

Slashes:
- Original: "Upper / Lower Split"
- Encoded: "Upper%20%2F%20Lower%20Split"

Parentheses:
- Original: "Bro Split (Body Part Split)"
- Encoded: "Bro%20Split%20%28Body%20Part%20Split%29"
```

---

## 🧪 Quick Test Commands

### Using cURL

```bash
# Test 1: Get all categories
curl "http://localhost:8000/api/categories"

# Test 2: Get Arnold Split
curl "http://localhost:8000/api/categories/Arnold%20Split"

# Test 3: Get PPL exercises
curl "http://localhost:8000/api/categories/Push%20%20Pull%20%20Legs%20%28PPL%29/exercises"

# Test 4: Get specific exercise
curl "http://localhost:8000/api/categories/Arnold%20Split/exercises/1"

# Test 5: Test slug format
curl "http://localhost:8000/api/categories/arnold-split"

# Test 6: Test error handling
curl "http://localhost:8000/api/categories/Invalid%20Plan"
```

---

## 📱 React Component Example

```jsx
import React, { useState, useEffect } from 'react';

const WorkoutPlans = () => {
  const [categories, setCategories] = useState([]);
  const [selectedPlan, setSelectedPlan] = useState(null);
  const [exercises, setExercises] = useState([]);
  const [loading, setLoading] = useState(false);

  // Load categories on component mount
  useEffect(() => {
    fetch('/api/categories')
      .then(res => res.json())
      .then(data => setCategories(data))
      .catch(err => console.error(err));
  }, []);

  // Load exercises when plan is selected
  const handleSelectPlan = async (planName) => {
    setSelectedPlan(planName);
    setLoading(true);
    
    try {
      const res = await fetch(`/api/categories/${encodeURIComponent(planName)}/exercises`);
      const data = await res.json();
      setExercises(data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <h1>Workout Plans</h1>
      
      <select onChange={(e) => handleSelectPlan(e.target.value)}>
        <option>Select a plan...</option>
        {categories.map(cat => (
          <option key={cat} value={cat}>{cat}</option>
        ))}
      </select>

      {selectedPlan && (
        <div>
          <h2>{selectedPlan}</h2>
          {loading ? (
            <p>Loading exercises...</p>
          ) : (
            <ul>
              {exercises.map(ex => (
                <li key={ex.id}>
                  {ex.name} - {ex.sets}x{ex.reps} ({ex.day})
                </li>
              ))}
            </ul>
          )}
        </div>
      )}
    </div>
  );
};

export default WorkoutPlans;
```

---

## 🛠️ Vue Component Example

```vue
<template>
  <div class="workout-plans">
    <h1>Workout Plans</h1>
    
    <select v-model="selectedPlan" @change="loadExercises">
      <option value="">Select a plan...</option>
      <option v-for="cat in categories" :key="cat" :value="cat">
        {{ cat }}
      </option>
    </select>

    <div v-if="selectedPlan" class="exercises">
      <h2>{{ selectedPlan }}</h2>
      <div v-if="loading" class="loading">Loading...</div>
      <ul v-else>
        <li v-for="ex in exercises" :key="ex.id" class="exercise">
          <strong>{{ ex.name }}</strong> - {{ ex.sets }}x{{ ex.reps }}
          <small>({{ ex.day }})</small>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      categories: [],
      selectedPlan: '',
      exercises: [],
      loading: false
    };
  },
  
  mounted() {
    this.loadCategories();
  },
  
  methods: {
    async loadCategories() {
      try {
        const res = await fetch('/api/categories');
        this.categories = await res.json();
      } catch (err) {
        console.error(err);
      }
    },
    
    async loadExercises() {
      if (!this.selectedPlan) return;
      
      this.loading = true;
      try {
        const url = `/api/categories/${encodeURIComponent(this.selectedPlan)}/exercises`;
        const res = await fetch(url);
        this.exercises = await res.json();
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.workout-plans { padding: 20px; }
select { padding: 10px; margin: 10px 0; }
.exercises { margin-top: 20px; }
.exercise { padding: 10px; border-bottom: 1px solid #eee; }
.loading { color: #999; font-style: italic; }
</style>
```

---

## 🎵 Angular Service Example

```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ExerciseService {
  private apiUrl = '/api';

  constructor(private http: HttpClient) { }

  getCategories(): Observable<string[]> {
    return this.http.get<string[]>(`${this.apiUrl}/categories`);
  }

  getCategory(planName: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/categories/${encodeURIComponent(planName)}`);
  }

  getAllExercises(planName: string): Observable<any[]> {
    return this.http.get<any[]>(
      `${this.apiUrl}/categories/${encodeURIComponent(planName)}/exercises`
    );
  }

  getExercise(planName: string, id: number): Observable<any> {
    return this.http.get(
      `${this.apiUrl}/categories/${encodeURIComponent(planName)}/exercises/${id}`
    );
  }
}
```

---

## 📋 Response Structure Reference

### Categories Response
```json
[
  "Arnold Split",
  "Bro Split (Body Part Split)",
  "Full Body Split",
  "Push  Pull  Legs (PPL)",
  "Upper / Lower Split"
]
```

### Full Plan Response
```json
{
  "plan_name": "Arnold Split",
  "days": {
    "day_name": {
      "exercises": [
        {
          "id": 1,
          "name": "Exercise Name",
          "video_url": "https://...",
          "sets": 3,
          "reps": "10",
          "rest_minutes": 2
        }
      ],
      "cardio": null
    }
  }
}
```

### Single Exercise Response
```json
{
  "id": 1,
  "name": "Bench Press",
  "video_url": "https://youtu.be/...",
  "sets": 3,
  "reps": 10,
  "rest_minutes": 2,
  "day": "chest & back"
}
```

---

## 🔒 Security Notes

1. **No Authentication Required** - These endpoints are public
2. **Read-Only** - No write operations available
3. **Input Validation** - Plan names are resolved with fallback
4. **Error Messages** - Generic 404s don't leak sensitive info

---

## 🚨 Common Issues & Solutions

### Issue: 404 Not Found
**Solution:** Check the plan name spelling and use URL encoding for spaces (%20)

### Issue: Invalid JSON Response
**Solution:** Verify JSON files are valid and stored in `storage/app/exercises/`

### Issue: Slow Response Times
**Solution:** Implement caching using Laravel Cache facade

### Issue: Special Characters Not Working
**Solution:** Use `encodeURIComponent()` in JavaScript or URL encoding in other languages

---

## 📞 Support Files

- **Full Documentation:** `EXERCISE_API_DOCUMENTATION.md`
- **Implementation Guide:** `IMPLEMENTATION_SUMMARY.md`
- **This Guide:** `QUICK_REFERENCE.md`

---

## ✅ What's Implemented

- ✅ Get all categories
- ✅ Get full plan details
- ✅ Get all exercises from a plan
- ✅ Get specific exercise by ID
- ✅ URL encoding support
- ✅ Slug support (hyphens to spaces)
- ✅ Case-insensitive matching
- ✅ Proper error handling (404s)
- ✅ JSON file storage
- ✅ Production-ready code

**No database needed! Everything works with JSON files.**

