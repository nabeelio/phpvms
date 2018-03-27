
const leaflet = require('leaflet');

import draw_base_map from './base_map'
import { ACTUAL_ROUTE_COLOR, PLAN_ROUTE_COLOR } from './config'

/**
 * Show some popup text when a feature is clicked on
 * @param feature
 * @param layer
 */
export const onFeaturePointClick = (feature, layer) => {
  let popup_html = '';
  if (feature.properties && feature.properties.popup) {
    popup_html += feature.properties.popup
  }

  layer.bindPopup(popup_html)
};

/**
 * Show each point as a marker
 * @param feature
 * @param latlng
 * @returns {*}
 */
export const pointToLayer = (feature, latlng) => {
  return leaflet.circleMarker(latlng, {
    radius: 12,
    fillColor: '#ff7800',
    color: '#000',
    weight: 1,
    opacity: 1,
    fillOpacity: 0.8
  })
}

/**
 *
 * @param opts
 * @private
 */
export default (opts) => {

  opts = Object.assign({
    route_points: null,
    planned_route_line: null,
    actual_route_points: null,
    actual_route_line: null,
    render_elem: 'map',
  }, opts);

  console.log(opts)

  let map = draw_base_map(opts)

  let geodesicLayer = leaflet.geodesic([], {
    weight: 7,
    opacity: 0.9,
    color: PLAN_ROUTE_COLOR,
    steps: 50,
    wrap: false,
  }).addTo(map)

  geodesicLayer.geoJson(opts.planned_route_line)

  try {
    map.fitBounds(geodesicLayer.getBounds())
  } catch (e) {
    console.log(e)
  }

  // Draw the route points after
  if (opts.route_points !== null) {
    let route_points = leaflet.geoJSON(opts.route_points, {
      onEachFeature: onFeaturePointClick,
      pointToLayer: pointToLayer,
      style: {
        'color': PLAN_ROUTE_COLOR,
        'weight': 5,
        'opacity': 0.65,
      },
    })

    route_points.addTo(map)
  }

  /**
   * draw the actual route
   */

  if (opts.actual_route_line !== null && opts.actual_route_line.features.length > 0) {
    let geodesicLayer = leaflet.geodesic([], {
      weight: 7,
      opacity: 0.9,
      color: ACTUAL_ROUTE_COLOR,
      steps: 50,
      wrap: false,
    }).addTo(map)

    geodesicLayer.geoJson(opts.actual_route_line)

    try {
      map.fitBounds(geodesicLayer.getBounds())
    } catch (e) {
      console.log(e)
    }
  }

  if (opts.actual_route_points !== null && opts.actual_route_points.features.length > 0) {
    let route_points = leaflet.geoJSON(opts.actual_route_points, {
      onEachFeature: onFeaturePointClick,
      pointToLayer: pointToLayer,
      style: {
        'color': ACTUAL_ROUTE_COLOR,
        'weight': 5,
        'opacity': 0.65,
      },
    })

    route_points.addTo(map)
  }
};
