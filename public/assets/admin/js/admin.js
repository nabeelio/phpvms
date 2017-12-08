/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(2);


/***/ }),
/* 1 */
/***/ (function(module, exports) {

/**
 * admin functions, mostly map/mapping related
 */

function phpvms_vacentral_airport_lookup(icao, callback) {
    $.ajax({
        url: '/api/airports/' + icao + '/lookup',
        method: 'GET'
    }).done(function (data, status) {
        callback(data.data);
    });
}

function phpvms_render_airspace_map(opts) {
    opts = __parse_opts(opts);
    var map = __draw_base_map(opts);
    if (opts.set_marker == true) {
        L.marker(coords).addTo(map);
    }
    return map;
}

function __parse_opts(opts) {
    _.defaults(opts, {
        render_elem: 'map',
        overlay_elem: '',
        lat: 0,
        lon: 0,
        zoom: 12,
        layers: [],
        set_marker: false
    });

    return opts;
}

function __draw_base_map(opts) {

    var coords = [opts.lat, opts.lon];

    /*var openaip_airspace_labels = new L.TileLayer.WMS(
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

    var opencyclemap_phys_osm = new L.TileLayer('http://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey=f09a38fa87514de4890fc96e7fe8ecb1', {
        maxZoom: 14,
        minZoom: 4,
        format: 'image/png',
        transparent: true
    });

    var openaip_cached_basemap = new L.TileLayer("http://{s}.tile.maps.openaip.net/geowebcache/service/tms/1.0.0/openaip_basemap@EPSG%3A900913@png/{z}/{x}/{y}.png", {
        maxZoom: 14,
        minZoom: 4,
        tms: true,
        detectRetina: false,
        subdomains: '12',
        format: 'image/png',
        transparent: true
    });

    var openaip_basemap_phys_osm = L.featureGroup([opencyclemap_phys_osm, openaip_cached_basemap]);

    var map = L.map('map', {
        layers: [openaip_basemap_phys_osm],
        center: coords,
        zoom: opts.zoom,
        scrollWheelZoom: false
    }).setView(coords, opts.zoom);

    var attrib = L.control.attribution({ position: 'bottomleft' });
    attrib.addAttribution("<a href=\"https://www.thunderforest.com\" target=\"_blank\" style=\"\">Thunderforest</a>");
    attrib.addAttribution("<a href=\"https://www.openaip.net\" target=\"_blank\" style=\"\">openAIP</a>");
    attrib.addAttribution("<a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\" style=\"\">OpenStreetMap</a> contributors");
    attrib.addAttribution("<a href=\"https://www.openweathermap.org\" target=\"_blank\" style=\"\">OpenWeatherMap</a>");

    attrib.addTo(map);

    return map;
}

/***/ }),
/* 2 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNGQ4ZGVjOGMzMDBjNTViZjg2YzkiLCJ3ZWJwYWNrOi8vLy4vcHVibGljL2pzL2FkbWluL2FkbWluLmpzIiwid2VicGFjazovLy8uL3B1YmxpYy9hc3NldHMvYWRtaW4vc2Fzcy9wYXBlci1kYXNoYm9hcmQuc2NzcyJdLCJuYW1lcyI6WyJwaHB2bXNfdmFjZW50cmFsX2FpcnBvcnRfbG9va3VwIiwiaWNhbyIsImNhbGxiYWNrIiwiJCIsImFqYXgiLCJ1cmwiLCJtZXRob2QiLCJkb25lIiwiZGF0YSIsInN0YXR1cyIsInBocHZtc19yZW5kZXJfYWlyc3BhY2VfbWFwIiwib3B0cyIsIl9fcGFyc2Vfb3B0cyIsIm1hcCIsIl9fZHJhd19iYXNlX21hcCIsInNldF9tYXJrZXIiLCJMIiwibWFya2VyIiwiY29vcmRzIiwiYWRkVG8iLCJfIiwiZGVmYXVsdHMiLCJyZW5kZXJfZWxlbSIsIm92ZXJsYXlfZWxlbSIsImxhdCIsImxvbiIsInpvb20iLCJsYXllcnMiLCJvcGVuY3ljbGVtYXBfcGh5c19vc20iLCJUaWxlTGF5ZXIiLCJtYXhab29tIiwibWluWm9vbSIsImZvcm1hdCIsInRyYW5zcGFyZW50Iiwib3BlbmFpcF9jYWNoZWRfYmFzZW1hcCIsInRtcyIsImRldGVjdFJldGluYSIsInN1YmRvbWFpbnMiLCJvcGVuYWlwX2Jhc2VtYXBfcGh5c19vc20iLCJmZWF0dXJlR3JvdXAiLCJjZW50ZXIiLCJzY3JvbGxXaGVlbFpvb20iLCJzZXRWaWV3IiwiYXR0cmliIiwiY29udHJvbCIsImF0dHJpYnV0aW9uIiwicG9zaXRpb24iLCJhZGRBdHRyaWJ1dGlvbiJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FB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QUFFQSxXQUFPQSxHQUFQO0FBQ0gsQzs7Ozs7O0FDMUZELHlDIiwiZmlsZSI6Ii9hc3NldHMvYWRtaW4vanMvYWRtaW4uanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAwKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA0ZDhkZWM4YzMwMGM1NWJmODZjOSIsIi8qKlxuICogYWRtaW4gZnVuY3Rpb25zLCBtb3N0bHkgbWFwL21hcHBpbmcgcmVsYXRlZFxuICovXG5cbmZ1bmN0aW9uIHBocHZtc192YWNlbnRyYWxfYWlycG9ydF9sb29rdXAoaWNhbywgY2FsbGJhY2spXG57XG4gICAgJC5hamF4KHtcbiAgICAgICAgdXJsOiAnL2FwaS9haXJwb3J0cy8nICsgaWNhbyArICcvbG9va3VwJyxcbiAgICAgICAgbWV0aG9kOiAnR0VUJ1xuICAgIH0pLmRvbmUoZnVuY3Rpb24gKGRhdGEsIHN0YXR1cykge1xuICAgICAgICBjYWxsYmFjayhkYXRhLmRhdGEpO1xuICAgIH0pO1xufVxuXG5mdW5jdGlvbiBwaHB2bXNfcmVuZGVyX2FpcnNwYWNlX21hcChvcHRzKVxue1xuICAgIG9wdHMgPSBfX3BhcnNlX29wdHMob3B0cyk7XG4gICAgdmFyIG1hcCA9IF9fZHJhd19iYXNlX21hcChvcHRzKTtcbiAgICBpZihvcHRzLnNldF9tYXJrZXIgPT0gdHJ1ZSkgeyBMLm1hcmtlcihjb29yZHMpLmFkZFRvKG1hcCk7IH1cbiAgICByZXR1cm4gbWFwO1xufVxuXG5mdW5jdGlvbiBfX3BhcnNlX29wdHMob3B0cykge1xuICAgIF8uZGVmYXVsdHMob3B0cywge1xuICAgICAgICByZW5kZXJfZWxlbTogJ21hcCcsXG4gICAgICAgIG92ZXJsYXlfZWxlbTogJycsXG4gICAgICAgIGxhdDogMCxcbiAgICAgICAgbG9uOiAwLFxuICAgICAgICB6b29tOiAxMixcbiAgICAgICAgbGF5ZXJzOiBbXSxcbiAgICAgICAgc2V0X21hcmtlcjogZmFsc2UsXG4gICAgfSk7XG5cbiAgICByZXR1cm4gb3B0cztcbn1cblxuZnVuY3Rpb24gX19kcmF3X2Jhc2VfbWFwKG9wdHMpIHtcblxuICAgIHZhciBjb29yZHMgPSBbb3B0cy5sYXQsIG9wdHMubG9uXTtcblxuICAgIC8qdmFyIG9wZW5haXBfYWlyc3BhY2VfbGFiZWxzID0gbmV3IEwuVGlsZUxheWVyLldNUyhcbiAgICAgICAgXCJodHRwOi8ve3N9LnRpbGUubWFwcy5vcGVuYWlwLm5ldC9nZW93ZWJjYWNoZS9zZXJ2aWNlL3dtc1wiLCB7XG4gICAgICAgICAgICBtYXhab29tOiAxNCxcbiAgICAgICAgICAgIG1pblpvb206IDEyLFxuICAgICAgICAgICAgbGF5ZXJzOiAnb3BlbmFpcF9hcHByb3ZlZF9haXJzcGFjZXNfbGFiZWxzJyxcbiAgICAgICAgICAgIHRpbGVTaXplOiAxMDI0LFxuICAgICAgICAgICAgZGV0ZWN0UmV0aW5hOiB0cnVlLFxuICAgICAgICAgICAgc3ViZG9tYWluczogJzEyJyxcbiAgICAgICAgICAgIGZvcm1hdDogJ2ltYWdlL3BuZycsXG4gICAgICAgICAgICB0cmFuc3BhcmVudDogdHJ1ZVxuICAgICAgICB9KTtcblxuICAgIG9wZW5haXBfYWlyc3BhY2VfbGFiZWxzLmFkZFRvKG1hcCk7Ki9cblxuICAgIHZhciBvcGVuY3ljbGVtYXBfcGh5c19vc20gPSBuZXcgTC5UaWxlTGF5ZXIoXG4gICAgICAgICdodHRwOi8ve3N9LnRpbGUudGh1bmRlcmZvcmVzdC5jb20vbGFuZHNjYXBlL3t6fS97eH0ve3l9LnBuZz9hcGlrZXk9ZjA5YTM4ZmE4NzUxNGRlNDg5MGZjOTZlN2ZlOGVjYjEnLCB7XG4gICAgICAgICAgICBtYXhab29tOiAxNCxcbiAgICAgICAgICAgIG1pblpvb206IDQsXG4gICAgICAgICAgICBmb3JtYXQ6ICdpbWFnZS9wbmcnLFxuICAgICAgICAgICAgdHJhbnNwYXJlbnQ6IHRydWVcbiAgICAgICAgfSk7XG5cbiAgICB2YXIgb3BlbmFpcF9jYWNoZWRfYmFzZW1hcCA9IG5ldyBMLlRpbGVMYXllcihcImh0dHA6Ly97c30udGlsZS5tYXBzLm9wZW5haXAubmV0L2dlb3dlYmNhY2hlL3NlcnZpY2UvdG1zLzEuMC4wL29wZW5haXBfYmFzZW1hcEBFUFNHJTNBOTAwOTEzQHBuZy97en0ve3h9L3t5fS5wbmdcIiwge1xuICAgICAgICBtYXhab29tOiAxNCxcbiAgICAgICAgbWluWm9vbTogNCxcbiAgICAgICAgdG1zOiB0cnVlLFxuICAgICAgICBkZXRlY3RSZXRpbmE6IGZhbHNlLFxuICAgICAgICBzdWJkb21haW5zOiAnMTInLFxuICAgICAgICBmb3JtYXQ6ICdpbWFnZS9wbmcnLFxuICAgICAgICB0cmFuc3BhcmVudDogdHJ1ZVxuICAgIH0pO1xuXG4gICAgdmFyIG9wZW5haXBfYmFzZW1hcF9waHlzX29zbSA9IEwuZmVhdHVyZUdyb3VwKFtvcGVuY3ljbGVtYXBfcGh5c19vc20sIG9wZW5haXBfY2FjaGVkX2Jhc2VtYXBdKTtcblxuICAgIHZhciBtYXAgPSBMLm1hcCgnbWFwJywge1xuICAgICAgICBsYXllcnM6IFtvcGVuYWlwX2Jhc2VtYXBfcGh5c19vc21dLFxuICAgICAgICBjZW50ZXI6IGNvb3JkcyxcbiAgICAgICAgem9vbTogb3B0cy56b29tLFxuICAgICAgICBzY3JvbGxXaGVlbFpvb206IGZhbHNlLFxuICAgIH0pLnNldFZpZXcoY29vcmRzLCBvcHRzLnpvb20pO1xuXG4gICAgdmFyIGF0dHJpYiA9IEwuY29udHJvbC5hdHRyaWJ1dGlvbih7cG9zaXRpb246ICdib3R0b21sZWZ0J30pO1xuICAgIGF0dHJpYi5hZGRBdHRyaWJ1dGlvbihcIjxhIGhyZWY9XFxcImh0dHBzOi8vd3d3LnRodW5kZXJmb3Jlc3QuY29tXFxcIiB0YXJnZXQ9XFxcIl9ibGFua1xcXCIgc3R5bGU9XFxcIlxcXCI+VGh1bmRlcmZvcmVzdDwvYT5cIik7XG4gICAgYXR0cmliLmFkZEF0dHJpYnV0aW9uKFwiPGEgaHJlZj1cXFwiaHR0cHM6Ly93d3cub3BlbmFpcC5uZXRcXFwiIHRhcmdldD1cXFwiX2JsYW5rXFxcIiBzdHlsZT1cXFwiXFxcIj5vcGVuQUlQPC9hPlwiKTtcbiAgICBhdHRyaWIuYWRkQXR0cmlidXRpb24oXCI8YSBocmVmPVxcXCJodHRwczovL3d3dy5vcGVuc3RyZWV0bWFwLm9yZy9jb3B5cmlnaHRcXFwiIHRhcmdldD1cXFwiX2JsYW5rXFxcIiBzdHlsZT1cXFwiXFxcIj5PcGVuU3RyZWV0TWFwPC9hPiBjb250cmlidXRvcnNcIik7XG4gICAgYXR0cmliLmFkZEF0dHJpYnV0aW9uKFwiPGEgaHJlZj1cXFwiaHR0cHM6Ly93d3cub3BlbndlYXRoZXJtYXAub3JnXFxcIiB0YXJnZXQ9XFxcIl9ibGFua1xcXCIgc3R5bGU9XFxcIlxcXCI+T3BlbldlYXRoZXJNYXA8L2E+XCIpO1xuXG4gICAgYXR0cmliLmFkZFRvKG1hcCk7XG5cbiAgICByZXR1cm4gbWFwO1xufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vcHVibGljL2pzL2FkbWluL2FkbWluLmpzIiwiLy8gcmVtb3ZlZCBieSBleHRyYWN0LXRleHQtd2VicGFjay1wbHVnaW5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL3B1YmxpYy9hc3NldHMvYWRtaW4vc2Fzcy9wYXBlci1kYXNoYm9hcmQuc2Nzc1xuLy8gbW9kdWxlIGlkID0gMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAiXSwic291cmNlUm9vdCI6IiJ9