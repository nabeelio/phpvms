
const rivets = require('rivets');

/**
 * Generic formatter to prepend
 *
 * @param value
 * @param prepend
 *
 * @returns {*}
 */
rivets.formatters.prepend = function (value, prepend) {
  return prepend + value;
};

/**
 * Format minutes into HHh MMm
 *
 * @param value
 *
 * @returns {string}
 */
rivets.formatters.time_hm = function (value) {
  const hours = Math.floor(value / 60);
  const mins = value % 60;
  return `${hours}h ${mins}m`;
};

/**
 *
 * @param value
 * @param len
 *
 * @returns {boolean}
 */
rivets.formatters.gt = (value, len) => value.length > len;

/**
 *
 * @param value
 * @param len
 *
 * @returns {boolean}
 */
rivets.formatters.lt = (value, len) => value.length < len;

/**
 *
 * @param value
 * @param len
 *
 * @returns {boolean}
 */
rivets.formatters.eq = (value, len) => value.length > len;

/**
 * Use a default value if value is null or blank
 *
 * @param value Value to use
 * @param def   Default value to use if value is null
 */
rivets.formatters.fallback = (value, def) => value || def;
