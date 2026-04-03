<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

// Read request and open db connection

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = getRequestPath();

if ($method !== 'GET') {
    respondJson(['error' => 'Method Not Allowed'], 405);
}

$pdo = getPdoConnection();
initializeDatabase($pdo);


// Match routes and handle requests

if (preg_match('#^/api/zones/?$#', $path)) {
    $summaryQuery = $pdo->query('SELECT id, name, type, status FROM parking_zones ORDER BY id');
    $zones = $summaryQuery->fetchAll(PDO::FETCH_ASSOC);

    respondJson($zones, 200);
}

if (preg_match('#^/api/zones/(\d+)/?$#', $path, $matches)) {
    $zoneId = (int) $matches[1];

    $detailQuery = $pdo->prepare('SELECT * FROM parking_zones WHERE id = :id');
    $detailQuery->execute(['id' => $zoneId]);

    $zone = $detailQuery->fetch(PDO::FETCH_ASSOC);

    if (!$zone) {
        respondJson(['error' => 'Zone not found'], 404);
    }

    $zone['id'] = (int) $zone['id'];
    $zone['max_capacity'] = (int) $zone['max_capacity'];
    $zone['hourly_rate_eur'] = (float) $zone['hourly_rate_eur'];
    $zone['latitude'] = (float) $zone['latitude'];
    $zone['longitude'] = (float) $zone['longitude'];
    $zone['amenities'] = json_decode((string) $zone['amenities'], true, 512, JSON_THROW_ON_ERROR);
    $zone['opening_hours'] = json_decode((string) $zone['opening_hours'], true, 512, JSON_THROW_ON_ERROR);

    respondJson($zone, 200);
}

respondJson(['error' => 'Not Found'], 404);

function getRequestPath(): string
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';

    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

    if ($basePath !== '' && $basePath !== '/' && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath));
    }

    return $path === '' ? '/' : $path;
}

function respondJson(array $payload, int $statusCode): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}