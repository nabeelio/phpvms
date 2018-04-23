/**
 * All of the functionality required for maps
 */

window.L = require('leaflet');
require('Leaflet.Geodesic');
require('leaflet-rotatedmarker');

import render_airspace_map from './airspace_map'
import render_live_map from './live_map'
import render_route_map from './route_map'

export {
  render_airspace_map,
  render_live_map,
  render_route_map,
}
