
const leaflet = require('leaflet');

import draw_base_map from './base_map'

/**
 * Render a map with the airspace, etc around a given set of coords
 * e.g, the airport map
 * @param opts
 */
export default (opts) => {
  opts = Object.assign({
    render_elem: 'map',
    overlay_elem: '',
    lat: 0,
    lon: 0,
    zoom: 12,
    layers: [],
    set_marker: true,
  }, opts);

  let map = draw_base_map(opts)
  const coords = [opts.lat, opts.lon]
  console.log('Applying coords', coords)

  map.setView(coords, opts.zoom)
  if (opts.set_marker === true) {
    leaflet.marker(coords).addTo(map)
  }

  return map
};
