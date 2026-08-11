<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

date_default_timezone_set('Asia/Manila');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'db.php';

try {

    /*
    |--------------------------------------------------------------------------
    | ADMIN SESSION CHECK
    |--------------------------------------------------------------------------
    */

    if (!isset($_SESSION['user_id'])) {

        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized access.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DATABASE CHECK
    |--------------------------------------------------------------------------
    */

    if (!isset($conn)) {

        throw new Exception(
            'Database connection was not found.'
        );
    }

    if ($conn->connect_error) {

        throw new Exception(
            'Database connection failed: ' .
            $conn->connect_error
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FETCH APPROVED / VALIDATED COMPLAINTS
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT

            rc.id,
            rc.ticket_no,
            rc.queue_no,
            rc.resident_id,

            rc.complaint_location,
            rc.category,
            rc.description,

            rc.action_status,
            rc.validation_status,

            rc.reviewed_by,
            rc.reviewed_at,

            rc.assigned_personnel_id,
            rc.assigned_personnel_type,
            rc.assigned_personnel_name,

            rc.assigned_by,
            rc.assigned_at,

            rc.resolved_by,
            rc.resolved_at,

            rc.submitted_at,

            rc.remarks,
            rc.admin_notes,


            /* RESIDENT */

            CONCAT(
                COALESCE(res.first_name, ''),
                CASE
                    WHEN
                        res.first_name IS NOT NULL
                        AND res.last_name IS NOT NULL
                        AND res.first_name <> ''
                        AND res.last_name <> ''
                    THEN ' '
                    ELSE ''
                END,
                COALESCE(res.last_name, '')
            ) AS resident_name,

            res.barangay AS barangay,


            /* VALIDATOR / REVIEWER */

            CONCAT(
    COALESCE(reviewer.first_name, ''),
    CASE
        WHEN
            reviewer.middle_initial IS NOT NULL
            AND TRIM(reviewer.middle_initial) <> ''
        THEN CONCAT(' ', TRIM(reviewer.middle_initial), '.')
        ELSE ''
    END,
    CASE
        WHEN
            reviewer.last_name IS NOT NULL
            AND TRIM(reviewer.last_name) <> ''
        THEN CONCAT(' ', TRIM(reviewer.last_name))
        ELSE ''
    END
) AS validated_by_name


        FROM resident_complaints rc


        LEFT JOIN users res
            ON res.id = rc.resident_id


        LEFT JOIN users reviewer
            ON reviewer.id = rc.reviewed_by


        WHERE
            rc.validation_status = 'Approved'


        ORDER BY
            rc.reviewed_at DESC,
            rc.id DESC
    ";


    /*
    |--------------------------------------------------------------------------
    | PREPARE
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        throw new Exception(
            'Failed to prepare query: ' .
            $conn->error
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXECUTE
    |--------------------------------------------------------------------------
    */

    if (!$stmt->execute()) {

        throw new Exception(
            'Failed to execute query: ' .
            $stmt->error
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE RESULT VARIABLES
    |--------------------------------------------------------------------------
    |
    | These are initialized before bind_result() so PHP/IDE static
    | analysis does not treat them as undefined or null-only variables.
    |
    */

    $id = null;
    $ticket_no = null;
    $queue_no = null;
    $resident_id = null;

    $complaint_location = null;
    $category = null;
    $description = null;

    $action_status = null;
    $validation_status = null;

    $reviewed_by = null;
    $reviewed_at = null;

    $assigned_personnel_id = null;
    $assigned_personnel_type = null;
    $assigned_personnel_name = null;

    $assigned_by = null;
    $assigned_at = null;

    $resolved_by = null;
    $resolved_at = null;

    $submitted_at = null;

    $remarks = null;
    $admin_notes = null;

    $resident_name = null;
    $barangay = null;

    $validated_by_name = null;


    /*
    |--------------------------------------------------------------------------
    | BIND RESULTS
    |--------------------------------------------------------------------------
    */

    if (!$stmt->bind_result(

        $id,
        $ticket_no,
        $queue_no,
        $resident_id,

        $complaint_location,
        $category,
        $description,

        $action_status,
        $validation_status,

        $reviewed_by,
        $reviewed_at,

        $assigned_personnel_id,
        $assigned_personnel_type,
        $assigned_personnel_name,

        $assigned_by,
        $assigned_at,

        $resolved_by,
        $resolved_at,

        $submitted_at,

        $remarks,
        $admin_notes,

        $resident_name,
        $barangay,

        $validated_by_name

    )) {

        throw new Exception(
            'Failed to bind query results: ' .
            $stmt->error
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FETCH DATA
    |--------------------------------------------------------------------------
    */

    $complaints = [];

    while ($stmt->fetch()) {

        $complaints[] = [

            'id' =>
                $id !== null
                    ? (int)$id
                    : 0,

            'ticket_no' =>
                $ticket_no ?? '',

            'queue_no' =>
                $queue_no !== null
                    ? (int)$queue_no
                    : 0,

            'resident_id' =>
                $resident_id !== null
                    ? (int)$resident_id
                    : 0,


            /*
            |--------------------------------------------------------------------------
            | RESIDENT
            |--------------------------------------------------------------------------
            */

            'resident_name' =>
                trim((string)($resident_name ?? '')) !== ''
                    ? trim((string)$resident_name)
                    : 'Unknown Resident',

            'barangay' =>
                trim((string)($barangay ?? '')) !== ''
                    ? trim((string)$barangay)
                    : 'Unknown Barangay',


            /*
            |--------------------------------------------------------------------------
            | COMPLAINT
            |--------------------------------------------------------------------------
            */

            'complaint_location' =>
                $complaint_location ?? '',

            'category' =>
                $category ?? '',

            'description' =>
                $description ?? '',

            'complaint' =>
                $description ?? '',


            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            'validation_status' =>
                $validation_status ?? '',

            'validated_by' =>
                $reviewed_by !== null
                    ? (int)$reviewed_by
                    : null,

            'validated_by_name' =>
                trim((string)($validated_by_name ?? '')) !== ''
                    ? trim((string)$validated_by_name)
                    : 'Unknown',

            'validated_at' =>
                $reviewed_at,

            'validation_date' =>
                $reviewed_at,


            /*
            |--------------------------------------------------------------------------
            | ACTION STATUS
            |--------------------------------------------------------------------------
            */

            'status' =>
                !empty($action_status)
                    ? $action_status
                    : 'Pending Assignment',

            'action_status' =>
                !empty($action_status)
                    ? $action_status
                    : 'Pending Assignment',


            /*
            |--------------------------------------------------------------------------
            | ASSIGNMENT
            |--------------------------------------------------------------------------
            */

            'assigned_personnel_id' =>
                $assigned_personnel_id !== null
                    ? (int)$assigned_personnel_id
                    : null,

            'assigned_personnel_type' =>
                $assigned_personnel_type ?? null,

            'assigned_personnel_name' =>
                $assigned_personnel_name ?? null,

            'assigned_at' =>
                $assigned_at,


            /*
            |--------------------------------------------------------------------------
            | RESOLUTION
            |--------------------------------------------------------------------------
            */

            'resolved_by' =>
                $resolved_by !== null
                    ? (int)$resolved_by
                    : null,

            'resolved_at' =>
                $resolved_at,


            /*
            |--------------------------------------------------------------------------
            | OTHER
            |--------------------------------------------------------------------------
            */

            'submitted_at' =>
                $submitted_at,

            'remarks' =>
                $remarks ?? '',

            'admin_notes' =>
                $admin_notes ?? ''
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE STATEMENT
    |--------------------------------------------------------------------------
    */

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */

    echo json_encode([

        'success' => true,

        'count' =>
            count($complaints),

        'complaints' =>
            $complaints

    ], JSON_UNESCAPED_UNICODE);


} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([

        'success' => false,

        'message' =>
            'Failed to fetch valid complaints.',

        'error' =>
            $e->getMessage(),

        'file' =>
            $e->getFile(),

        'line' =>
            $e->getLine()

    ], JSON_UNESCAPED_UNICODE);

    exit;
}
