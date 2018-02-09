# Changelog

## Alpha 2

!! Please do a full reinstall, with recreating the database

- Bump minimum PHP version to 7.1, since 7.0 is already deprecated - [#166](https://github.com/nabeelio/phpvms/issues/166)
- Add a `SKIN_NAME` template variable to reference the current skin, vs hardcoding the skin name in the templates
- PIREP hours can't be changed after it's no longer in a pending state
- DB: `airport.tz` to `airport.timezone`
- API: Most calls, with exception of ACARS, are now private and require an API key to access
- API: Allow a `fields` object to set custom PIREP fields, also returns the current values

### Fixes

- PIREP fields being set when filing manually is working
- Field for the rank's image changed to string input

***

## Alpha 1 (2018-02-04, v7.0.0-alpha1)

- Initial Release
