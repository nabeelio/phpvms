## Alpha 2

!! Please do a full reinstall, with recreating the database

- Bump minimum PHP version to 7.1, since 7.0 is already deprecated - [#166](https://github.com/nabeelio/phpvms/issues/166)
- Upgraded to Laravel 5.6
- Installer: Updated to create `config.php` file, to override config file values without having to modify the config files themselves
- Installer: Moved most options into the `config.php`, out of the `env.php` file
- Admin: Set the country for the airline [#191](https://github.com/nabeelio/phpvms/issues/191)
- Admin: Add ranks from the subfleet edit page
- Admin: Added flight time field to flight add/edit page
- Admin: PIREP hours can't be changed after it's no longer in a pending state
- Admin: Removed the tail number field
- DB: `airport.tz` to `airport.timezone`
- DB: Removed `aircaft.tail_number`
- DB: Decimal type field sizes shrunk to the default sizes
- DB: Removed the `raw_data` field from the PIREPs table
- API: All units expected in imperial (distance in nautical miles, fuel in lbs, mass in lbs)
- API: Added ability to add/remove bids for users
- API: Added a setting to only show aircraft that are at the departure airport of a flight [#171](https://github.com/nabeelio/phpvms/issues/171)
- API: Most calls, with exception of ACARS, are now private and require an API key to access [#173](https://github.com/nabeelio/phpvms/issues/173)
- API: Create an `/api/flight/:id/route` call to return the route information for a flight [#183](https://github.com/nabeelio/phpvms/issues/183)
- API: Allow a `fields` object to set custom PIREP fields, also returns the current values
- API: `level` not required in prefile anymore
- Setting: Restrict to aircraft that are at a flight's departure airport [#171](https://github.com/nabeelio/phpvms/issues/171)
- Setting: Implementation of filtering flights that are only at the user's current airport [#174](https://github.com/nabeelio/phpvms/issues/174)
- Templates: Add a `SKIN_NAME` template variable to reference the current skin, vs hardcoding the skin name in the templates
- Console: Added `php artisan phpvms:dev-install` command which creates the config files and creates the database/inserts sample data in one command [#176](https://github.com/nabeelio/phpvms/issues/176)
- Rank aircraft restrictions are properly working now [#170](https://github.com/nabeelio/phpvms/issues/170)

#### Fixes

- PIREP fields being set when filing manually is working
- ACARS data wasn't being ordered properly, causing issues on the map [77055991](https://github.com/nabeelio/phpvms/commit/77055991af36877552e1921466987d3066774d6b)
- Field for the rank's image changed to string input [b5dbde8](https://github.com/nabeelio/phpvms/commit/b5dbde84c4c786799f474117381b8227642f0777)
- Set a default value for a setting [#106](https://github.com/nabeelio/phpvms/issues/106)
- Admin: Rank image field fixed
- API: Only active airlines are returned
- API; Return errors if user isn't allowed on the submitted aircraft [#170](https://github.com/nabeelio/phpvms/issues/170)
- API: Fixed typo from `subfleet` to `subfleets` in the `/api/flights` call(s) [f6b2102](https://github.com/nabeelio/phpvms/commit/f6b2102e4827da6177eb4eee0c3ce0d38eb78ce3)
- API: Wrapped all calls in a `data` field
- API: `planned_distance` and `planned_flight_time` fields are now optional
- Setting: Subfleets returned in the flight calls respect the `pireps.restrict_aircraft_to_rank` setting [#170](https://github.com/nabeelio/phpvms/issues/170)

------

## Alpha 1 (2018-02-04, v7.0.0-alpha1)

- Initial Release
