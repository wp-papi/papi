import $ from 'jquery';

const options = {
  templateSelection: function (result) {
    if (!result.id) {
      return result.text;
    }

    const url      = $(result.element).data('edit-url');
    const editText = papiL10n.edit;
    const $html = $('<div><span>' + result.text + '</span> <a href="#" class="papi-iframe-link-open" data-url="' + url + '&papi-iframe-mode=true">' + editText + '</a></div>');

    $html.find('a').on('mousedown', function(e) {
      e.stopPropagation();
    }).on('click', function(e) {
      e.preventDefault();
    });

    return $html;
  }
};

export default options;
