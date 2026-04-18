<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionOption;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // Gender Question
        $gender = Question::create([
            'key' => 'gender',
            'title' => 'What is your gender?',
            'type' => 'select',
            'order' => 1,
        ]);

        QuestionOption::insert([
            ['question_id' => $gender->id, 'label' => 'Male', 'value' => 'male'],
            ['question_id' => $gender->id, 'label' => 'Female', 'value' => 'female'],
        ]);

        // Age Question
        Question::create([
            'key' => 'age',
            'title' => 'How old are you?',
            'type' => 'number',
            'order' => 2,
        ]);

        // Goal Question
        $goal = Question::create([
            'key' => 'goal',
            'title' => 'What is your main goal?',
            'type' => 'select',
            'order' => 3,
        ]);

        // Weight Question
        Question::create([
            'key' => 'weight',
            'title' => 'What is your weight (kg)?',
            'type' => 'number',
            'order' => 4,
        ]);

        // Height Question
        Question::create([
            'key' => 'height',
            'title' => 'What is your height (cm)?',
            'type' => 'number',
            'order' => 5,
        ]);

        
        QuestionOption::insert([
            ['question_id' => $goal->id, 'label' => 'Lose Weight', 'value' => 'lose'],
            ['question_id' => $goal->id, 'label' => 'Build Muscle', 'value' => 'muscle'],
        ]);
    }
}
