# Changelog
## Alpha 2

!! Please do a full reinstall, with recreating the database

- Bump minimum PHP version to 7.1, since 7.0 is already deprecated - [#166](https://github.com/nabeelio/phpvms/issues/166)
- Add a `SKIN_NAME` template variable to reference the current skin, vs hardcoding the skin name in the templates
- PIREP hours can't be changed after it's no longer in a pending state
- DB: `airport.tz` to `airport.timezone`
- API: Added a setting to only show aircraft that are at the departure airport of a flight [#171](https://github.com/nabeelio/phpvms/issues/171)
- API: Most calls, with exception of ACARS, are now private and require an API key to access [#173](https://github.com/nabeelio/phpvms/issues/173)
- API: Allow a `fields` object to set custom PIREP fields, also returns the current values
- Setting: Restrict to aircraft that are at a flight's departure airport [#171](https://github.com/nabeelio/phpvms/issues/171)
- Setting: Implementation of filtering flights that are only at the user's current airport [#174](https://github.com/nabeelio/phpvms/issues/174)

#### Fixes

- PIREP fields being set when filing manually is working
- Field for the rank's image changed to string input [b5dbde8](https://github.com/nabeelio/phpvms/commit/b5dbde84c4c786799f474117381b8227642f0777)
- Set a default value for a setting [#106](https://github.com/nabeelio/phpvms/issues/106)
- API: Fixed typo from `subfleet` to `subfleets` in the `/api/flights` call(s) [f6b2102](https://github.com/nabeelio/phpvms/commit/f6b2102e4827da6177eb4eee0c3ce0d38eb78ce3)
- Setting: Subfleets returned in the flight calls respect the `pireps.restrict_aircraft_to_rank` setting [#170](https://github.com/nabeelio/phpvms/issues/170)

***

## Alpha 1 (2018-02-04, v7.0.0-alpha1)

- Initial Release
