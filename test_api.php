<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\ExerciseService;

$service = new ExerciseService();
$data = $service->fetchAllExercises();

echo "API Response Structure:\n";
var_dump($data);

if (is_array($data) && count($data) > 0) {
    echo "\nFirst exercise structure:\n";
    var_dump($data[0]);
}
