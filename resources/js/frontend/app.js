
// Import the bids functionality
import { addBid, removeBid } from './bids';

require('./../bootstrap');

window.phpvms.bids = {
  addBid,
  removeBid,
};

// Import the mapping function
window.phpvms.map = require('../maps/index');
