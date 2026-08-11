<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

include 'db.php';

while (true) {

    $result = $conn->query("
    SELECT 
        t.id AS truck_id,
        t.plate_no,

        u.id AS collector_id,
        CONCAT(u.first_name, ' ', u.last_name) AS collector_name,

        tl.lat,
        tl.lng,
        tl.location,
        tl.capacity,
        tl.last_updated,

        s.start_time,
        s.end_time,
        s.barangay,
        s.garbage_type

    FROM trucks t

    LEFT JOIN users u
        ON t.collector_id = u.id

    LEFT JOIN truck_locations tl
        ON t.id = tl.truck_id

    INNER JOIN schedules s
        ON s.truck_id = t.id
        AND s.day_of_week = DAYNAME(CURDATE())


    ");

    $trucks = [];

    while($row = $result->fetch_assoc()){

        // default values if no realtime location yet
        $row['lat'] = $row['lat'] ?? null;
        $row['lng'] = $row['lng'] ?? null;
        $row['location'] = $row['location'] ?? "Waiting for location";
        $row['capacity'] = $row['capacity'] ?? 0;
        $row['last_updated'] = $row['last_updated'] ?? null;

        $trucks[] = $row;
    }


    echo "data: " . json_encode($trucks) . "\n\n";

    ob_flush();
    flush();

    sleep(5);
}
?>