/**
 * Before you edit these, read the documentation on how these files are compiled:
 * https://docs.phpvms.net/developers/building-assets
 *
 * Edits here don't take place until you compile these assets and then upload them.
 */

// Import the bids functionality
import { addBid, removeBid } from './bids';

require('./../bootstrap');

window.phpvms.bids = {
  addBid,
  removeBid,
};

// Import the mapping function
window.phpvms.map = require('../maps/index');
