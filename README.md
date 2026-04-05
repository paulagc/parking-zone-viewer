# Parking Zone Viewer

## Run with Docker

This project includes a simple container setup for local development.

### Start

```bash
docker compose up --build
```
API will be available at:

```http://localhost:8080/api/zones
http://localhost:8080/api/zones?q=kamppi
http://localhost:8080/api/zones/1
```

Frontend will be available at:
```
http://localhost:5173
```

### Stop

```bash
docker compose down
```

## Postman collection

A Postman collection to import can be found at `backend/postman-collection/parking-api.postman_collection.json` . It uses the `localhost` variable with default value 8080.


## Improvements

Backend
- Separate MVC layers.
- Improving the search with filtering by other variables e.g. price or type, adding sorting asc/desc.
- Integration tests for the endpoints.

Frontend
- When clearing search field reload the whole zone list again.
- Unselect the detailed zone when searching or clicking out of the zone.
- Search by just typing in the field (suggestion list) with a delay so it doesn't bloat requests.
- Style: responsive layout with separate UIs for desktop, mobile or multiple viewpoints.

## AI use
Using GitHub Copilot on Visual Studio Code for:
- Create mock parking data.
- Generate unit tests for the backend.
- Generate Docker file
- Some regex and SQL expressions, automatic code completion and suggestions.

