<?php

namespace App\Services;

class ExerciseService
{
    /**
     * Get all categories (slugs)
     */
    public function getAllCategories(): array
    {
        $files = glob(storage_path('app/exercises/*.json'));

        return array_map(function ($file) {
            return basename($file, '.json');
        }, $files);
    }

    /**
     * Get category
     */
    public function getCategory(string $planName): ?array
    {
        $filePath = $this->getFilePath($planName);

        if (!$filePath || !file_exists($filePath)) {
            return null;
        }

        return json_decode(file_get_contents($filePath), true);
    }

    /**
     * Get exercise by ID
     */
    public function getExercise(string $planName, int $id): ?array
    {
        $category = $this->getCategory($planName);

        if (!$category) {
            return null;
        }

        foreach ($category['days'] as $day => $dayData) {
            foreach ($dayData['exercises'] as $exercise) {
                if ($exercise['id'] == $id) {
                    return array_merge($exercise, ['day' => $day]);
                }
            }
        }

        return null;
    }

    /**
     * Get all exercises
     */
    public function getAllExercises(string $planName): ?array
    {
        $category = $this->getCategory($planName);

        if (!$category) {
            return null;
        }

        $result = [];

        foreach ($category['days'] as $day => $dayData) {
            foreach ($dayData['exercises'] as $exercise) {
                $result[] = array_merge($exercise, ['day' => $day]);
            }
        }

        return $result;
    }

    /**
     * Get correct file path
     */
    private function getFilePath(string $planName): ?string
    {
        $files = glob(storage_path('app/exercises/*.json'));

        $input = strtolower(trim($planName));

        foreach ($files as $file) {
            $filename = basename($file, '.json');

            if (strtolower($filename) === $input) {
                return $file;
            }
        }

        return null;
    }
}
