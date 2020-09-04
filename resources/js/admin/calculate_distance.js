/**
 * Before you edit these, read the documentation on how these files are compiled:
 * https://docs.phpvms.net/developers/building-assets
 *
 * Edits here don't take place until you compile these assets and then upload them.
 */

import request from '../request';

/**
 * Lookup an airport from the server
 *
 * @param {String} fromICAO
 * @param {String} toICAO
 */
export default async (fromICAO, toICAO) => {
  const params = {
    method: 'GET',
    url: `/api/airports/${fromICAO}/distance/${toICAO}`,
  };

  const response = await request(params);
  return response.data;
};
