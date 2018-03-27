
const leaflet = require('leaflet');

export default (opts) => {

  opts = Object.assign({
    render_elem: 'map',
    center: [29.98139, -95.33374],
    zoom: 5,
    maxZoom: 10,
    layers: [],
    set_marker: false,
  }, opts);

  let feature_groups = [];
  /*var openaip_airspace_labels = new leaflet.TileLayer.WMS(
      "http://{s}.tile.maps.openaip.net/geowebcache/service/wms", {
          maxZoom: 14,
          minZoom: 12,
          layers: 'openaip_approved_airspaces_labels',
          tileSize: 1024,
          detectRetina: true,
          subdomains: '12',
          format: 'image/png',
          transparent: true
      });

  openaip_airspace_labels.addTo(map);*/

  const opencyclemap_phys_osm = new leaflet.TileLayer(
    'http://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey=f09a38fa87514de4890fc96e7fe8ecb1', {
      maxZoom: 14,
      minZoom: 4,
      format: 'image/png',
      transparent: true
    })

  feature_groups.push(opencyclemap_phys_osm)

  /*const openaip_cached_basemap = new leaflet.TileLayer("http://{s}.tile.maps.openaip.net/geowebcache/service/tms/1.0.0/openaip_basemap@EPSG%3A900913@png/{z}/{x}/{y}.png", {
      maxZoom: 14,
      minZoom: 4,
      tms: true,
      detectRetina: true,
      subdomains: '12',
      format: 'image/png',
      transparent: true
  });

  feature_groups.push(openaip_cached_basemap);
  */

  const openaip_basemap_phys_osm = leaflet.featureGroup(feature_groups)

  let map = leaflet.map('map', {
    layers: [openaip_basemap_phys_osm],
    center: opts.center,
    zoom: opts.zoom,
    scrollWheelZoom: false,
  })

  const attrib = leaflet.control.attribution({position: 'bottomleft'})
  attrib.addAttribution('<a href="https://www.thunderforest.com" target="_blank" style="">Thunderforest</a>')
  attrib.addAttribution('<a href="https://www.openaip.net" target="_blank" style="">openAIP</a>')
  attrib.addAttribution('<a href="https://www.openstreetmap.org/copyright" target="_blank" style="">OpenStreetMap</a> contributors')
  attrib.addAttribution('<a href="https://www.openweathermap.org" target="_blank" style="">OpenWeatherMap</a>')

  attrib.addTo(map)

  return map
};
