phpVMS has a REST-api for integration with any standalone programs, including any flight simulator addons, or ACARS applications.

# Authentication and Authorization

Each user is given an API key (and can regenerate it) when they register. Requests to a phpVMS API will require an `X-API-Key` header, with this key. Addons can take advantage of this by adding the `api.auth` middleware to their route group.

### Headers Example

```http
X-API-Key: {user API key}
Content-type: application/json
```

## Sample cURL Request

```php
$api_key = 'YOUR API KEY';
$url = "http://your-site.com/api/user";
$headers = [
    'X-API-Key:' . $api_key,
    'Content-type:application/json',
];

$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

json_response = \json_decode(curl_exec($ch));
curl_close($ch);

echo $json_response;
```
## Types

Not all IDs are numeric integers. At the moment, the primary keys on these tables are strings. They are hashed IDs that are generated:

* acars
* flights
* pireps

## Errors

Where possible, the standard HTTP error codes are followed and returned, with extra information in the body, if available.

### Unauthorized

`401` is returned if the API key is invalid, or the user is disallowed from API access. The `message` parameter will offer more error.

### Not Found

`404` is returned if an entity is not found

### Validation Errors

`400`, with details in the `message` parameter about the bad input.

## Pagination

Where indicated, pagination is enabled/available. When calling those APIs, the data is returned in this format:

- `data` contains a list of all of the objects (for example, the airports)
- `links` contains the links to navigate through the paginated list
- `meta` contains information about the current dataset

```json
{ 
  "data": [ ... ],
  "links": {
    "first":"http://phpvms.test/api/airports?page=1",
    "last":"http://phpvms.test/api/airports?page=3",
    "prev":null,
    "next":"http://phpvms.test/api/airports?page=2"
  },
  "meta": {
    "current_page": 1,
    "from":1, 
    "last_page":3,
    "path":"http://phpvms.test/api/airports",
    "per_page":50,
    "to":50,
    "total":120
  }
}
```

# APIs Available

## User

```http
GET /api/user - Returns the user's information, including bids, etc
```

***

## Airlines

```http
GET /api/airlines
```
Get all of the airlines

```http
GET /api/airlines/{ID}
```
Get information about a specific airline

***

## Airports

```http
GET /api/airports
```
Get all of the airports, paginated list

```http
GET /api/airports/hubs
```
Get all of the hubs, paginated list

```http
GET /api/airports/{ICAO}
```
Get the details about an airport

```http
GET /api/airports/{ICAO}/lookup
```
Get the details about an airport, but proxies the call to vaCentral

***

## Fleet

```http
GET /api/fleet
```
Get all of the subfleets and aircraft under the fleet. Includes the fare and airline information. Paginated

```http
GET /api/aircraft/{id}
````
Return information about an aircraft, including the subfleet information

Query string parameters: `?type=registration|tail_number|icao`. Default/blank is the DB ID

***

## Flights


```http
GET /api/flights
```
Return all of the flights, paginated


```http
GET /api/flights/{FLIGHT ID}
```
Return details about a given flight


```http
GET /api/flights/search
```
Do a search for a flight

Query String Example:
`/api/flights/search?depicao=KJFK&arricao=KAUS`

  - `airline_id` - ID of the airline
  - `dep_icao` - Departure airport code
  - `arr_icao` - Arrival airport code
  - `flight_number` - Can be a partial match
  - `route_code`

***

## PIREPs

```http
GET /api/pireps/{PIREP ID}
```
Retrieve the PIREP information

```http
GET /api/pireps/{PIREP ID}/route
```
Retrieve the route

```http
GET /api/pireps/{PIREP ID}/acars/geojson
```
Get the ACARS data in GeoJSON format

```http
GET /api/pireps/{PIREP ID}/acars/positions
```
Get the ACARS data in plain rows

[See the ACARS documentation](https://github.com/nabeelio/phpvms/wiki/acars) for details about the PIREPs and ACARS API details

***

## Users

```http
GET /api/users/{id}
```
Retrieve info about a user

```http
GET /api/users/{id}/bids
```
Get a user's bids

***

## Settings

```http
GET /api/settings
```
Get all of the phpVMS configuration settings, including things like the units and individual configuration options. [See the list of settings here](https://github.com/nabeelio/phpvms/blob/master/app/Database/migrations/2017_06_07_014930_create_settings_table.php#L41). Remember to look at the `type` column in order to properly parse the value.
