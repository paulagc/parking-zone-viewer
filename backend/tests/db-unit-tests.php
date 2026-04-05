<?php
declare(strict_types=1);

require_once __DIR__ . '/../database.php';

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function assertSame(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

function createInMemoryPdo(): PDO
{
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
}

function testInitializeDatabaseSeedsExactlyFiveZones(): void
{
    $pdo = createInMemoryPdo();

    initializeDatabase($pdo);

    $count = (int) $pdo->query('SELECT COUNT(*) FROM parking_zones')->fetchColumn();
    assertSame(5, $count, 'initializeDatabase() should seed 5 zones on empty database');
}

function testInitializeDatabaseDoesNotDuplicate(): void
{
    $pdo = createInMemoryPdo();

    initializeDatabase($pdo);
    initializeDatabase($pdo);

    $count = (int) $pdo->query('SELECT COUNT(*) FROM parking_zones')->fetchColumn();
    assertSame(5, $count, 'initializeDatabase() should not duplicate seed rows when run twice');
}

function testSeedRowsContainAllRequiredKeys(): void
{
    $pdo = createInMemoryPdo();

    initializeDatabase($pdo);

    $rows = $pdo->query('SELECT * FROM parking_zones ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    assertTrue(count($rows) > 0, 'Seeded rows should exist');

    $requiredKeys = [
        'id',
        'name',
        'type',
        'status',
        'description',
        'max_capacity',
        'hourly_rate_eur',
        'latitude',
        'longitude',
        'amenities',
        'opening_hours',
    ];

    foreach ($rows as $index => $row) {
        foreach ($requiredKeys as $requiredKey) {
            assertTrue(
                array_key_exists($requiredKey, $row),
                "Row {$index} is missing required key: {$requiredKey}"
            );
        }
    }
}

$tests = [
    'testInitializeDatabaseSeedsExactlyFiveZones',
    'testInitializeDatabaseDoesNotDuplicate',
    'testSeedRowsContainAllRequiredKeys',
];

foreach ($tests as $test) {
    $test();
    echo "[PASS] {$test}\n";
}

echo "Unit tests completed successfully.\n";