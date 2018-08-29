import $ from 'jquery';

export default function (selector) {
  return {
    templateSelection: function (result) {
      // placeholder.
      if (result.id === '') {
        result.element = $(selector).find('[data-placeholder]');
      }

      let htmlText = '<div><span>' + result.text + '</span> <!--edit--> <!--new--></div>';

      const editUrl = $(result.element).data('edit-url');
      if (editUrl) {
        const editText = papiL10n.edit;
        const editHtml = '<a href="#" class="papi-iframe-link-open" data-url="' + editUrl + '" data-title="' + editText + '" data-selector="' + selector.name + '">' + editText + '</a>';
        htmlText = htmlText.replace('<!--edit-->', editHtml);
      }

      const newUrl = $(result.element).data('new-url');
      if (newUrl) {
        const newText = papiL10n.new;
        const newHtml = '<a href="#" class="papi-iframe-link-open" data-url="' + newUrl + '" data-title="' + newText + '" data-selector="' + selector.name + '">' + newText + '</a>';
        htmlText = htmlText.replace('<!--new-->', newHtml);
      }

      if (typeof htmlText !== 'undefined') {
        const $html = $(htmlText);

        $html.find('a').on('mousedown', function(e) {
          e.stopPropagation();
        }).on('click', function(e) {
          e.preventDefault();
        });

        return $html;
      }

      return result.text;
    }
  }
};
