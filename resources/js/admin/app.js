/**
 * Admin stuff needed
 */


require('./../bootstrap');

import airport_lookup from "./airport_lookup";
import calculate_distance from "./calculate_distance";

window.phpvms.airport_lookup = airport_lookup;
window.phpvms.calculate_distance = calculate_distance;

// Import the mapping function
window.phpvms.map = require('../maps/index');
