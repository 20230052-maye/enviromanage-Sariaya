<?php
header('Content-Type: application/json');

$query = strtolower($_GET['query'] ?? '');

/* TITLES */
$data = [
  "Collection Operations" => [
    "Collection Delay Alert",
    "Collection Completed",
    "Missed Collection Notice"
  ],

  "Truck & Route Management" => [
    "Truck Breakdown Notice",
    "Route Change Advisory"
  ],

  "Emergency & Weather Operations" => [
    "Emergency Collection Suspension",
    "Weather-Related Delay"
  ],

  "System & Technical Alerts" => [
    "System Maintenance Alert",
    "Mobile App Downtime Notice"
  ]
];

$result = [];

/* flatten + filter */
foreach ($data as $group => $titles) {
  foreach ($titles as $title) {
    if (strpos(strtolower($title), $query) !== false) {
      $result[] = $title;
    }
  }
}

echo json_encode(array_slice($result, 0, 5));