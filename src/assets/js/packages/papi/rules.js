import $ from 'jquery';

class Rules {

  /**
   * Initialize Papi rules class.
   */

  static init() {
    new Rules().binds();
  }

  /**
   * Bind rule.
   *
   * @param {string} slug
   * @param {object} rule
   * @param {string} relation
   */

  bindRule(slug, rule, rules) {
    const $target   = $('[name="' + slug + '"]');
    const selector  = '[name="' + rule.slug + '"], [data-papi-rule="' + rule.slug + '"]';
    const self      = this;
    let typingTimer;

    $('body').on('keyup change', selector, function(e) {
      let $this  = $(this);
      let val    = self.getValue(rule.slug);
      let values = {};

      for (let key in rules) {
        let rule = rules[key];

        if ($.type(rule) !== 'object') {
          continue;
        }

        values[key] = rule;

        if (values[key].source == null) {
          values[key].source = self.getValue(rule.slug);
        }
      }

      let attr = {
        rules:    values,
        $target:  $target
      };

      if (e.type === 'change') {
        self.display(attr);
      } else {
        self.debounce(function() {
          self.display(attr);
        }, 200);
      }
    });
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    const self = this;
    $('[data-papi-rules="true"]').each(function() {
      self.setupRules($(this));
    });
  }

  /**
   * Debounce function after
   * given wait time.
   *
   * @param {funciton} fn
   * @param {int} wait
   * @param {bool} immediate
   */

  debounce(fn, wait, immediate) {
    let timeout;
    (function() {
      const context = this;
      const args    = arguments;
      const later   = function() {
        timeout = null;
        if (!immediate) {
          fn.apply(context, args);
        }
      };
      const callNow = immediate && !timeout;

      clearTimeout(timeout);
      timeout = setTimeout(later, wait);

      if (callNow) {
        fn.apply(context, args);
      }
    })();
  }

  /**
   * Display field or hide.
   *
   * @param {object} options
   */

  display(options) {
    this.fetch(options, function(res) {
      options.$target.closest('.papi-rules-exists')[res.render ? 'removeClass' : 'addClass']('papi-hide');
    });
  }

  /**
   * Fetch rule result from Papi ajax.
   *
   * @param {object} options
   * @param {function} callback
   */

  fetch(options, callback) {
    const params = {
      'action':   'get_rules_result',
      'page_type': 'pages/article-page-type',
      'post':      $('#post_ID').val()
    };
    const data   = {
      'rules':     options.rules,
      'slug':      options.$target.attr('name')
    };

    $.ajax({
      type:     'POST',
      data:     {
        data: JSON.stringify(data)
      },
      dataType: 'json',
      url:      papi.ajaxUrl + '?' + $.param(params)
    }).success(callback);
  }

  /**
   * Get value from field.
   *
   * @param {string} slug
   */

  getValue(slug) {
    const selector  = '[data-papi-rule="' + slug + '"], [name="' + slug + '"]';
    const $prop     = $(selector).filter(function () {
      let $this = $(this);
      return $this.data('papi-rule-value') || $(this).val();
    });
    let   val       = $prop.data('papi-rule-value');

    if (val === undefined) {
      val = $prop.val();
    }

    switch ($prop.attr('type')) {
      case 'checkbox':
        val = $prop.is(':checked') ? 'true' : 'false';
        break;
      default:
        break;
    }

    return val;
  }

  /**
   * Setup rules.
   *
   * @param {object} $this
   */

  setupRules($this) {
    const self     = this;
    const slug     = $this.data().papiSlug;
    const rules    = $.parseJSON($this.text());

    for (let key in rules) {
      let rule = rules[key];

      if ($.type(rule) !== 'object') {
        continue;
      }

      this.bindRule(slug, rule, rules);
    }
  }

}

export default Rules;
