import $ from 'jquery';

class Taxonomy {

  /**
   * Initialize Papi taxonomy class.
   */
  static init() {
    new Taxonomy().binds();
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $('#submit').on('click', this.addNewTerm.bind(this));
  }

  /**
   * Redirect if a new term is added and redirect is activated.
   *
   * @param {object} e
   */
  addNewTerm(e) {
    e.preventDefault();

    const $title    = $('#tag-name');
    const title     = $title.val();
    const $pageType = $('[data-papi-page-type-key=true]');

    if (!$pageType.data('redirect') && !$pageType.find(':selected').data('redirect')) {
      return;
    }

    if (!title.length) {
      return;
    }

    let interval;

    interval = setInterval(() => {
      if ($('#ajax-response').children().length) {
        clearInterval(interval);
        return;
      }

      const $thelist   = $('#the-list');
      const $rowTitles = $thelist.find('td.column-name a.row-title');
      const $rows      = $rowTitles.contents().filter(function () {
        return $(this).text().trim() === title.trim();
      });

      if ($rows.length) {
        clearInterval(interval);
        window.location = $rows[0].parentElement.href;
      }
    }, 500);
  }
}

export default Taxonomy;
