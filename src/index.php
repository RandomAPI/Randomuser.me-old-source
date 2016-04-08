<?php
/*
    Random User Generator web interface.
*/

error_reporting(0);
require_once("Dataset.class.php");

header('content-type: text/plain; charset=utf-8');
header("access-control-allow-origin: *");

// Available parameters
$results = $_GET['results'];
$seed    = $_GET['seed'];
$lego    = $_GET['lego'];
$gender  = $_GET['gender'];
$format  = $_GET['format'];
$nat     = $_GET['nat'];

if ($nat === null) {
    $nat = strtolower($_GET['nationality']);
}

$dataset = new Dataset($seed);

// Only accept numeric results and use default if not provided
if ($results === null || !is_numeric($results)) {
    $results = 1;
}

// Sanitize gender input. If it isn't valid, then it is null (random)
// A set seed will override the gender
if ($gender !== "male" && $gender !== "female" && isset($_GET['gender']) && $_GET['gender'] !== null || $seed !== null) {
    $dataset->setGender(null);
} else {
    $dataset->setGender($gender);
}

// Set format
$dataset->setFormat($format);

// Choose random nat if not provided or invalid
// Lego mode overrides ALL
if (isset($lego)) {
    $dataset->setNat("lego");
} else if ((($nat === null || !$dataset->validNat($nat)) && $dataset->getNat() === null)) {
    $dataset->chooseRandomNat();
} else {
      if ($dataset->getNat() === null) {
            $dataset->setNat($nat);
    }
}

$dataset->generate($results);
echo $dataset->output();
?>
