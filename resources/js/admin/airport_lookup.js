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
 * @param {String} icao
 */
export default async (icao) => {
  const params = {
    method: 'GET',
    url: `/api/airports/${icao}/lookup`,
  };

  const response = await request(params);
  console.log('lookup raw response: ', response);
  return response.data;
};
