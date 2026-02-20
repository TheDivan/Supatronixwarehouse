<?php
// CLI script to update office titles in the DB.
// Usage: php storage/update_offices.php

$config_path = __DIR__ . '/../application/config/database.php';
$defaults = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'bookingsoftware'
];

if (file_exists($config_path)) {
    $contents = file_get_contents($config_path);
    if (preg_match("/'hostname'\s*=>\s*'([^']*)'/", $contents, $m)) $defaults['hostname'] = $m[1];
    if (preg_match("/'username'\s*=>\s*'([^']*)'/", $contents, $m)) $defaults['username'] = $m[1];
    if (preg_match("/'password'\s*=>\s*'([^']*)'/", $contents, $m)) $defaults['password'] = $m[1];
    if (preg_match("/'database'\s*=>\s*'([^']*)'/", $contents, $m)) $defaults['database'] = $m[1];
}

echo "Using DB host={$defaults['hostname']} db={$defaults['database']} user={$defaults['username']}\n";

$mysqli = new mysqli($defaults['hostname'], $defaults['username'], $defaults['password'], $defaults['database']);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (".$mysqli->connect_errno.") ". $mysqli->connect_error ."\n";
    exit(1);
}

$queries = [
    // explicit replacements
    "UPDATE offices SET title = 'Walvis Bay' WHERE title = 'Dallas, Texas';",
    "UPDATE offices SET title = 'Swakopmund' WHERE title = 'Kuala Lumpur, Malaysia';",
    // fuzzy matches
    "UPDATE offices SET title = 'Walvis Bay' WHERE title LIKE '%Dallas%';",
    "UPDATE offices SET title = 'Swakopmund' WHERE title LIKE '%Kuala%';",
    // remove country suffix if present
    "UPDATE offices SET title = 'Walvis Bay' WHERE title LIKE 'Walvis Bay%';",
    "UPDATE offices SET title = 'Swakopmund' WHERE title LIKE 'Swakopmund%';",
];

foreach ($queries as $sql) {
    if ($mysqli->query($sql) === TRUE) {
        $affected = $mysqli->affected_rows;
        echo "Query OK — affected rows: $affected — ".trim($sql)."\n";
    } else {
        echo "Error executing: " . $sql . " — " . $mysqli->error . "\n";
    }
}

echo "Done. Verify offices table: SELECT * FROM offices;\n";
$mysqli->close();
