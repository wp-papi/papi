import $ from 'jquery';

/**
 * Property Relationship.
 */
class Relationship {

  /**
   * Initialize Property Relationship.
   */
  static init() {
    new Relationship().binds();
  }
  /**
   * Add new page to the list.
   *
   * @param {object} e
   */
  add(e) {
    e.preventDefault();

    const $this    = $(e.currentTarget);
    const $li      = $this.clone();
    const $prop    = $this.closest('.papi-property-relationship');
    const $right   = $prop.find('.relationship-right');
    const $list    = $right.find('ul');
    const settings = $prop.data().settings;
    const limit    = settings.limit;
    const append   = limit === undefined || limit === -1 || $list.find('li').length < limit;

    if (append) {
      $li.find('span.icon').removeClass('plus').addClass('minus');
      $li.find('input').attr('name', $li.find('input').data().name);

      $li.appendTo($list);
      this.triggerRule($prop);

      if (settings.onlyOnce) {
        $this.addClass('papi-hide');
      }
    }
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $('.relationship-right > ul').sortable({
      placeholder: 'ui-state-highlight',
      start: function (e, ui) {
        ui.item.addClass('sortable');
      },
      stop: function (e, ui) {
        ui.item.removeClass('sortable');
      }
    }).disableSelection();

    $(document).on('click', '.papi-property-relationship .relationship-left li', this.add.bind(this));
    $(document).on('click', '.papi-property-relationship .relationship-right li', this.remove.bind(this));
    $(document).on('keyup', '.papi-property-relationship input[type=search]', this.search.bind(this));
    $(document).on('papi/property/repeater/added', '[data-property="relationship"]', this.update.bind(this));
  }

  /**
   * Remove the selected page.
   *
   * @param {object} e
   */
  remove(e) {
    const $this    = $(e.currentTarget);
    const $prop    = $this.closest('.papi-property-relationship');
    const settings = $prop.data().settings;

    if (settings.onlyOnce) {
      $prop
        .find('.relationship-left input[value="' + $this.find('input[type=hidden]').val() + '"]')
        .closest('li')
        .removeClass('papi-hide');
    }

    $this.remove();
    this.triggerRule($prop);
  }

  /**
   * Search for a page in the list.
   *
   * @param {object} e
   */
  search(e) {
    e.preventDefault();

    const $this = $(e.currentTarget);
    const $list = $this.closest('.papi-property-relationship').find('.relationship-left ul');
    const val   = $this.val().toLowerCase();

    $list.find('li').each(function () {
      let $li = $(this);
      if ($li.text().toLowerCase().indexOf(val) === -1) {
        $li.addClass('papi-hide');
      } else {
        $li.removeClass('papi-hide');
      }
    });
  }

  /**
   * Trigger conditional rule.
   *
   * @param {object} $prop
   */
  triggerRule($prop) {
    $prop
      .find('[data-papi-rule]')
      .trigger('change');
  }

  /**
   * Fix name attribute when added to a repeater.
   *
   * @param {object} e
   */
  update(e) {
    e.preventDefault();

    let $this   = $(e.currentTarget);
    const $prop = $this.prev();

    $prop.find('.relationship-left [name]').each(function () {
      $this = $(this);
      $this.data('name', $this.attr('name'));
      $this.removeAttr('name');
    });
  }
}

export default Relationship;
