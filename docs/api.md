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

### GET /api/user

Returns the user's information, including bids, etc. Example response:

```json
{
   "id":2,
   "name":"Adam Lockman PhD",
   "email":"miles.sporer@example.net",
   "apikey":null,
   "rank_id":"1",
   "home_airport": "KJFK",
   "curr_airport": "KJFK",
   "last_pirep_id": 1,
   "flights":0,
   "flight_time":914,
   "balance":0,
   "timezone": "American/Chicago",
   "status":0,
   "state":1,
   "airline":{
      "id":1,
      "icao":"VMS",
      "iata":"VMS",
      "name":"phpVMS Airlines",
      "country":"United States",
      "logo":null
   },
   "bids":[

   ],
   "rank":{
      "name":"New Pilot",
      "subfleets":[

      ]
   }
}
```
***

## Airlines

### GET /api/airlines

Get all of the airlines. Paginated

```json
{
   "data":[
      {
         "id":1,
         "icao": "VMS",
         "iata": "VMS",
         "name": "phpVMS Airlines",
         "country": "United States",
         "logo": null
      }
   ],
   "links":{
      "first":"http://phpvms.test/api/airlines?page=1",
      "last":"http://phpvms.test/api/airlines?page=1",
      "prev":null,
      "next":null
   },
   "meta":{
      "current_page":1,
      "from":1,
      "last_page":1,
      "path":"http://phpvms.test/api/airlines",
      "per_page":50,
      "to":1,
      "total":1
   }
}
```

### GET /api/airlines/{ID}

Get information about a specific airline

```json
{
    "id":1,
    "icao": "VMS",
    "iata": "VMS",
    "name": "phpVMS Airlines",
    "country": "United States",
    "logo": null
}
```

***

## Airports

### GET /api/airports

Get all of the airports, paginated list

```json
{
   "data":[
      {
         "id":"KJFK",
         "iata":"KJFK",
         "icao":"KJFK",
         "name":"John F Kennedy International Airport",
         "city":"New York",
         "country":"United States",
         "location":null,
         "hub": true,
         "fuel_100ll_cost": 0.00,
         "fuel_jeta_cost": 0.00,
         "fuel_mogas_cost": 0.00,
         "tz":"America/New_York",
         "lat":40.6398,
         "lon":-73.7789
      }
   ],
   "links":{
      "first":"http://phpvms.test/api/airports?page=1",
      "last":"http://phpvms.test/api/airports?page=2",
      "prev":null,
      "next":"http://phpvms.test/api/airports?page=2"
   },
   "meta":{
      "current_page":1,
      "from":1,
      "last_page":2,
      "path":"http://phpvms.test/api/airports",
      "per_page":50,
      "to":2,
      "total":2
   }
}
```

### GET /api/airports/hubs

Get all of the hubs, paginated list

```json
{
   "data":[
      {
         "id":"KJFK",
         "iata":"KJFK",
         "icao":"KJFK",
         "name":"John F Kennedy International Airport",
         "city":"New York",
         "country":"United States",
         "location":null,
         "hub": true,
         "fuel_100ll_cost": 0.00,
         "fuel_jeta_cost": 0.00,
         "fuel_mogas_cost": 0.00,
         "tz":"America/New_York",
         "lat":40.6398,
         "lon":-73.7789
      }
   ],
   "links":{
      "first":"http://phpvms.test/api/airports?page=1",
      "last":"http://phpvms.test/api/airports?page=2",
      "prev":null,
      "next":"http://phpvms.test/api/airports?page=2"
   },
   "meta":{
      "current_page":1,
      "from":1,
      "last_page":2,
      "path":"http://phpvms.test/api/airports",
      "per_page":50,
      "to":2,
      "total":2
   }
}
```

### GET /api/airports/{ICAO}

Get the details about an airport

```json
{
    "id":"KJFK",
    "iata":"KJFK",
    "icao":"KJFK",
    "name":"John F Kennedy International Airport",
    "city":"New York",
    "country":"United States",
    "location":null,
    "hub": true,
    "fuel_100ll_cost": 0.00,
    "fuel_jeta_cost": 0.00,
    "fuel_mogas_cost": 0.00,
    "tz":"America/New_York",
    "lat":40.6398,
    "lon":-73.7789
}
```

***

## Fleet

### GET /api/fleet

Get all of the subfleets and aircraft under the fleet. Includes the fare and airline information. Paginated

```json
{
   "data":[
      {
         "id":1,
         "airline_id":1,
         "name":"Boeing 747-400",
         "type":"B744",
         "fuel_type":null,
         "cargo_capacity":null,
         "fuel_capacity":null,
         "gross_weight":null,
         "aircraft":[
            {
               "id":1,
               "subfleet_id":"1",
               "icao":"B744",
               "airport_id":"KJFK",
               "hex_code":null,
               "name":"Boeing 747 \"The Queen\"",
               "registration":"NC17",
               "tail_number":"17",
               "active":true,
               "created_at":"2018-01-08 21:37:13",
               "updated_at":"2018-01-08 21:37:13"
            }
         ]
      }
   ],
   "links":{
      "first":"http://phpvms.test/api/fleet?page=1",
      "last":"http://phpvms.test/api/fleet?page=1",
      "prev":null,
      "next":null
   },
   "meta":{
      "current_page":1,
      "from":1,
      "last_page":1,
      "path":"http://phpvms.test/api/fleet",
      "per_page":50,
      "to":2,
      "total":2
   }
}
```

### GET /api/aircraft/{id}

Return information about an aircraft, including the subfleet information

Query string parameters: `?type=registration|tail_number|icao`. Default/blank is the DB ID

```json
{
    "id":1,
    "subfleet_id":"1",
    "icao":"B744",
    "airport_id":"KJFK",
    "hex_code":null,
    "name":"Boeing 747 \"The Queen\"",
    "registration":"NC17",
    "tail_number":"17",
    "active":true,
    "subfleet": {
        "id":1,
        "airline_id":1,
        "name":"Boeing 747-400",
        "type":"B744",
        "fuel_type":null,
        "cargo_capacity":null,
        "fuel_capacity":null,
        "gross_weight":null
    },
    "created_at":"2018-01-08 21:37:13",
    "updated_at":"2018-01-08 21:37:13"
}
```

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
