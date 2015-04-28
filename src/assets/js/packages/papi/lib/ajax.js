import $ from 'jquery';

class Ajax {

  /**
   * Get properties from Papi ajax.
   *
   * @param {object} data
   * @param {function} callback
   */

  static getProperties(data, callback) {
    $.ajax({
      type: 'POST',
      data: JSON.stringify(data),
      url: papi.ajaxUrl + '?action=get_properties'
    }).success(callback);
  }

}

export default Ajax;
