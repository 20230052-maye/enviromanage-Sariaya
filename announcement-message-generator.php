<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$title = strtolower($data['title'] ?? '');

$templates = [

  /* COLLECTION */
  "collection delay alert" =>
    "Waste collection may be delayed today due to operational or weather-related conditions.",

  "collection completed" =>
    "Waste collection operations for today have been successfully completed.",

  "missed collection notice" =>
    "Some areas were not serviced during today's collection schedule. Follow-up collection will be arranged.",

  /* TRUCK */
  "truck breakdown notice" =>
    "One of the collection vehicles is currently under maintenance which may cause delays.",

  "route change advisory" =>
    "Collection routes have been temporarily adjusted due to road conditions or traffic restrictions.",

  /* EMERGENCY */
  "emergency collection suspension" =>
    "Collection services are temporarily suspended due to severe weather conditions.",

  "weather-related delay" =>
    "Heavy rainfall and flooding may affect today's waste collection schedule.",

  /* SYSTEM */
  "system maintenance alert" =>
    "EnviroManage services will undergo scheduled maintenance tonight.",

  "mobile app downtime notice" =>
    "The mobile application is currently experiencing temporary technical issues."
];

/* default fallback */
$response = "Please stay informed regarding this announcement.";

/* match title */
foreach ($templates as $key => $message) {
  if (strpos($title, $key) !== false) {
    $response = $message;
    break;
  }
}

echo json_encode([
  "message" => $response
]);