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

    const $this  = $(this);
    const $li    = $this.clone();
    const $right = $this.closest('.papi-property-relationship').find('.relationship-right');
    const $list  = $right.find('ul');
    const limit  = $right.data().limit;
    const append = limit === undefined || limit === -1 || $list.find('li').length < limit;

    if (append) {
      $li.find('span.icon').removeClass('plus').addClass('minus');
      $li.find('input').attr('name', $li.find('input').data().name);

      $li.appendTo($list);
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
    $(document).on('click', '.papi-property-relationship .relationship-left li', this.add);
    $(document).on('click', '.papi-property-relationship .relationship-right li', this.remove);
    $(document).on('keyup', '.papi-property-relationship input[type=search]', this.search);
    $(document).on('papi/property/repeater/added', '[data-property="relationship"]', this.update);
  }

  /**
   * Remove the selected page.
   *
   * @parma {object} e
   */

  remove(e) {
    e.preventDefault();

    $(this).remove();
  }

  /**
   * Search for a page in the list.
   *
   * @parma {object} e
   */

  search(e) {
    e.preventDefault();

    const $this = $(this);
    const $list = $this.closest('.papi-property-relationship').find('.relationshio-left ul');
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
   * Fix name attribute when added to a repeater.
   *
   * @parma {object} e
   */

  update(e) {
    e.preventDefault();

    let   $this = $(this);
    const $prop = $this.prev();

    $prop.find('.relationship-left [name]').each(function () {
      $this = $(this);
      $this.data('name', $this.attr('name'));
      $this.removeAttr('name');
    });
  }

}

export default Relationship;
