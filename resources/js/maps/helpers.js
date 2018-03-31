
const leaflet = require('leaflet');

/**
 * Add a WMS layer to a map. opts must be:
 * {
 *  url: '',
 *  params: {}
 *  }
 * @param map
 * @param opts
 */
export function addWMSLayer(map, opts) {

    if(opts.url === '') {
        return;
    }

    opts.params = Object.assign({
        format: 'image/png',
        transparent: true,
        maxZoom: 14,
        minZoom: 4,
    }, opts.params);

    const mlayer = leaflet.tileLayer.wms(
        opts.url, opts.params
    );

    mlayer.addTo(map);

    return mlayer;
}

/**
 * Show a popup
 * @param feature
 * @param layer
 */
export function showFeaturePopup(feature, layer) {
    let popup_html = '';
    if (feature.properties && feature.properties.popup) {
        popup_html += feature.properties.popup
    }

    layer.bindPopup(popup_html)
}
