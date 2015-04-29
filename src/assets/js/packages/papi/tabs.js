import $ from 'jquery';

class Tabs {

  /**
   * Initialize Papi tabs class.
   */

  static init() {
    let tabs = new Tabs();

    tabs.binds();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $('a[data-papi-tab]').on('click', this.changeTab);
  }

  /**
   * Change tab.
   *
   * @param {object} e
   */

  changeTab(e) {
    e.preventDefault();

    let $this = $(this);
    let tab = $this.data().papiTab;

    $('a[data-papi-tab]').parent().removeClass('active');
    $this.parent().addClass('active');

    $('div[data-papi-tab]').removeClass('active').addClass('.papi-hide');
    $('div[data-papi-tab="' + tab + '"]').addClass('active').removeClass('papi-hide');
  }

}

export default Tabs;
