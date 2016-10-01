import $ from 'jquery';

const options = {
  templateSelection: function (result) {
    console.log(result);

    if (!result.id) {
      return result.text;
    }

    const url      = $(result.element).data('edit-url');
    const editText = 'Edit'; // TODO: translate

    return $('<span>' + result.text + '</span> <a href="#" class="papi-iframe-link-open" data-url="' + url + '&papi-iframe-mode=true">' + editText + '</a>');
  }
};

export default options;
