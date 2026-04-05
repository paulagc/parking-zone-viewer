<?php
declare(strict_types=1);

function getPdoConnection(): PDO
{
    $dbDir = __DIR__ . '/database';

    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0777, true);
    }

    $pdo = new PDO('sqlite:' . $dbDir . '/parkman.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
}

function initializeDatabase(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS parking_zones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            type TEXT NOT NULL,
            status TEXT NOT NULL,
            description TEXT NOT NULL,
            max_capacity INTEGER NOT NULL,
            hourly_rate_eur REAL NOT NULL,
            latitude REAL NOT NULL,
            longitude REAL NOT NULL,
            amenities TEXT NOT NULL,
            opening_hours TEXT NOT NULL
        )'
    );

    $existingCount = (int) $pdo->query('SELECT COUNT(*) FROM parking_zones')->fetchColumn();

    if ($existingCount > 0) {
        return;
    }

    $seedZones = [
        [
            'name' => 'Kamppi Center',
            'type' => 'commercial',
            'status' => 'active',
            'description' => 'Underground parking facility in the heart of Helsinki with EV charging and 24/7 access.',
            'max_capacity' => 450,
            'hourly_rate_eur' => 4.50,
            'latitude' => 60.1685,
            'longitude' => 24.9318,
            'amenities' => ['EV Charging', 'Disabled Access', '24/7 Open', 'Security Cameras'],
            'opening_hours' => ['weekdays' => '06:00–23:00', 'weekends' => '08:00–23:00'],
        ],
        [
            'name' => 'Esplanadi Park',
            'type' => 'street',
            'status' => 'active',
            'description' => 'Street-side paid parking zone near Esplanadi with short-walk access to central shopping and offices.',
            'max_capacity' => 120,
            'hourly_rate_eur' => 3.80,
            'latitude' => 60.1676,
            'longitude' => 24.9450,
            'amenities' => ['Pay-by-App', 'Wheelchair Access', 'Well Lit'],
            'opening_hours' => ['weekdays' => '07:00–22:00', 'weekends' => '09:00–22:00'],
        ],
        [
            'name' => 'Pasila Tripla Garage',
            'type' => 'commercial',
            'status' => 'active',
            'description' => 'Multi-level indoor parking under Mall of Tripla with direct elevator access to the station and shops.',
            'max_capacity' => 700,
            'hourly_rate_eur' => 3.20,
            'latitude' => 60.1987,
            'longitude' => 24.9338,
            'amenities' => ['EV Charging', 'CCTV', 'Indoor Parking', 'License Plate Recognition'],
            'opening_hours' => ['weekdays' => '05:30–00:30', 'weekends' => '07:00–00:30'],
        ],
        [
            'name' => 'Katajanokka Harbor Zone',
            'type' => 'street',
            'status' => 'active',
            'description' => 'Harbor-adjacent parking for ferry passengers and local residents, with clear wayfinding to terminals.',
            'max_capacity' => 180,
            'hourly_rate_eur' => 3.00,
            'latitude' => 60.1653,
            'longitude' => 24.9682,
            'amenities' => ['Pay-by-App', 'Disabled Bays', 'Harbor Shuttle Stop Nearby'],
            'opening_hours' => ['weekdays' => '06:00–23:00', 'weekends' => '06:00–23:00'],
        ],
        [
            'name' => 'Itis Pysäköinti',
            'type' => 'commercial',
            'status' => 'active',
            'description' => 'Covered mall parking in Itäkeskus with family spots, EV charging, and direct access to metro services.',
            'max_capacity' => 520,
            'hourly_rate_eur' => 2.80,
            'latitude' => 60.2097,
            'longitude' => 25.0823,
            'amenities' => ['Family Parking', 'EV Charging', 'Indoor Parking', 'Restrooms Nearby'],
            'opening_hours' => ['weekdays' => '06:00–23:30', 'weekends' => '08:00–23:30'],
        ],
    ];

    $insertQuery = $pdo->prepare(
        'INSERT INTO parking_zones (
            name,
            type,
            status,
            description,
            max_capacity,
            hourly_rate_eur,
            latitude,
            longitude,
            amenities,
            opening_hours
        ) VALUES (
            :name,
            :type,
            :status,
            :description,
            :max_capacity,
            :hourly_rate_eur,
            :latitude,
            :longitude,
            :amenities,
            :opening_hours
        )'
    );

    foreach ($seedZones as $zone) {
        $insertQuery->execute([
            'name' => $zone['name'],
            'type' => $zone['type'],
            'status' => $zone['status'],
            'description' => $zone['description'],
            'max_capacity' => $zone['max_capacity'],
            'hourly_rate_eur' => $zone['hourly_rate_eur'],
            'latitude' => $zone['latitude'],
            'longitude' => $zone['longitude'],
            'amenities' => json_encode($zone['amenities'], JSON_THROW_ON_ERROR),
            'opening_hours' => json_encode($zone['opening_hours'], JSON_THROW_ON_ERROR),
        ]);
    }
}