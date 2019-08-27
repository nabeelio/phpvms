
require('./../bootstrap');

// Import the bids functionality
import {addBid, removeBid} from './bids';
window.phpvms.bids = {
    addBid,
    removeBid,
};

// Import the mapping function
window.phpvms.map = require('../maps/index');
