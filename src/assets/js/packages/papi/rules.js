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
   */

  bindRule(slug, rule) {
    const $target   = $('[name="' + slug + '"]');
    const $source   = $('[name="' + rule.slug + '"]');
    const self      = this;
    const ev        = $source.prop('tagName').toLowerCase() === 'select' ? 'change' : 'keyup';
    let typingTimer;

    $source.on(ev, function (e) {
      let $this = $(this);
      let attr  = {
        slug:    slug,
        $target: $target,
        val:     $this.val()
      };

      if (e.type === 'change') {
        self.display(attr);
      } else {
        self.debounce(function () {
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
    $('[data-papi-rules="true"]').each(function () {
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
    this.fetch(options.slug, options.val, function (res) {
      options.$target.closest('tr')[res.render ? 'removeClass' : 'addClass']('papi-hide');
    });
  }

  /**
   * Fetch rule result from Papi ajax.
   *
   * @param {string} slug
   * @param {mixed} value
   * @param {function} callback
   */

  fetch(slug, value, callback) {
    const params = {
      'action':    'get_rules_result',
      'page_type': 'pages/article-page-type',
      'page_id':   $('#post_ID').val(),
      'slug':      slug,
      'value':     value
    };

    $.ajax({
      type: 'GET',
      dataType: 'json',
      url: papi.ajaxUrl + '?' + $.param(params)
    }).success(callback);
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
    const relation = rules.relation;

    delete rules.relation;

    for (let key in rules) {
      let rule = rules[key];

      if ($.type(rule) !== 'object') {
        continue;
      }

      this.bindRule(slug, rule);
    }
  }

}

export default Rules;
