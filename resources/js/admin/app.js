/**
 * Before you edit these, read the documentation on how these files are compiled:
 * https://docs.phpvms.net/developers/building-assets
 *
 * Edits here don't take place until you compile these assets and then upload them.
 */

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
