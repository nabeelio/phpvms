/**
 * All of the functionality required for maps
 */

import render_airspace_map from './airspace_map';
import render_live_map from './live_map';
import render_route_map from './route_map';

require('Leaflet.Geodesic');
require('leaflet-rotatedmarker');

window.L = require('leaflet');

export {
  render_airspace_map,
  render_live_map,
  render_route_map,
};
