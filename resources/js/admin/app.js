/**
 * Admin stuff needed
 */


import airport_lookup from './airport_lookup';
import calculate_distance from './calculate_distance';

require('./../bootstrap');

window.phpvms.airport_lookup = airport_lookup;
window.phpvms.calculate_distance = calculate_distance;

// Import the mapping function
window.phpvms.map = require('../maps/index');
