import $ from 'jquery';

class Tabs {

  /**
   * Initialize Papi tabs class.
   */
  static init() {
    new Tabs().binds();
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $('a[data-papi-tab]').on('click', this.changeTab.bind(this));
    this.updateTabsTableBack($('ul.papi-tabs li.active'));
  }

  /**
   * Change tab.
   *
   * @param {object} e
   */
  changeTab(e) {
    e.preventDefault();

    const $this = $(e.currentTarget);
    const $parent = $this.parent();
    const tab = $this.data().papiTab;

    $('a[data-papi-tab]').parent().removeClass('active');
    $parent.addClass('active');

    $('div[data-papi-tab]').removeClass('active').addClass('papi-hide');
    const $tabContent = $('div[data-papi-tab="' + tab + '"]').addClass('active').removeClass('papi-hide');

    const forceUpdate = $('.papi-tabs-content').height() < $('.papi-tabs').height() &&
      !$tabContent.find('tr').last().find('.papi-table-sidebar').length;

    this.updateTabsTableBack($parent, forceUpdate);
  }

  /**
   * Update tabs table back css class.
   *
   * @param {object} $activeTab
   * @param {bool} addClass
   */
  updateTabsTableBack($activeTab, addClass = false) {
    const $tabsTableBack = $activeTab.closest('.papi-tabs-wrapper').find('.papi-tabs-table-back');
    $tabsTableBack[$activeTab.hasClass('white-tab') || addClass ? 'addClass' : 'removeClass']('white-tab');
  }
}

export default Tabs;
