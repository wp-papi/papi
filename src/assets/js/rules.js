import $ from 'jquery';
import Utils from 'utils';

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
    const ruleSlug  = this.getRuleSlug(slug, rule);
    const $target   = this.getTarget(slug);
    const selector  = this.getSelector(ruleSlug);
    const self      = this;

    $('body').on('keyup change', selector, function(e) {
      const source = $('[data-papi-rule-source-slug="' + slug + '"]').text();
      let values   = {};

      if (!source.length) {
        return;
      }

      const rules = $.parseJSON(source);

      for (let key in rules) {
        let rule = rules[key];

        if ($.type(rule) !== 'object') {
          continue;
        }

        values[key] = rule;
        values[key].slug = ruleSlug;

        if (values[key].source == null) {
          values[key].source = self.getValue(rule.slug);
        }
      }

      let attr = {
        rules: values,
        slug: slug,
        $target: $target
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
    $('body').on('init', '[data-papi-rules="true"]', function() {
      $('[data-papi-rules="true"]').each(function() {
        self.setupRules($(this));
      });
    });
    $('[data-papi-rules="true"]').trigger('init');
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
      'action': 'get_rules_result',
      'page_type': this.getPageTypeId(),
      'post': $('#post_ID').val()
    };
    const data = {
      'rules': options.rules,
      'slug': options.$target.attr('name')
    };

    $.ajax({
      type: 'POST',
      data: {
        data: JSON.stringify(data)
      },
      dataType: 'json',
      url: papi.ajaxUrl + '?' + $.param(params)
    }).success(callback);
  }

  /**
   * Get Page Type id.
   *
   * @return {string}
   */
  getPageTypeId() {
    let pageType = Utils.getParameterByName('page_type');

    if (!pageType.length) {
      pageType = $('[data-papi-page-type-key="true"]').val();
    }

    return pageType;
  }

  /**
   * Get rule slug.
   *
   * @param {string} slug
   * @param {object} rule
   *
   * @return string
   */
  getRuleSlug(slug, rule) {
    const arrReg  = /\[\d+\](\[\w+\])$/;
    const papiReg = /^papi_/;

    if (rule.slug.indexOf('.') !== -1) {
      rule.slug = rule.slug.split('.')[0];
    }

    if (arrReg.test(slug)) {
      slug = slug.replace(arrReg.exec(slug)[1], '[' + rule.slug.replace(papiReg, '') + ']');

      if ($('[name="' + slug + '"]').length) {
        rule.slug = slug;
      }
    }

    return rule.slug;
  }

  /**
   * Get selector for rule slug.
   *
   * @param {string} ruleSlug
   *
   * @return {string}
   */
  getSelector(ruleSlug) {
    return '[name="' + ruleSlug + '[]"], [name="' + ruleSlug + '"], [data-papi-rule="' + ruleSlug + '"]';
  }

  /**
   * Get property target.
   *
   * @param {string} slug
   *
   * @return {object}
   */
  getTarget(slug) {
    let $target = $('[name="' + slug + '"]');

    if (!$target.length && slug.substr(-1) !== ']') {
      $target = $('[name="' + slug + '[]"]');
    }

    if (!$target.length) {
      $target = $('[data-papi-rule="' + slug + '"]');
      $target.attr('name', slug);
    }

    return $target;
  }

  /**
   * Get value from field.
   *
   * @param {string} slug
   */
  getValue(slug) {
    const selector = this.getSelector(slug);
    let $prop = $('[name="' + slug + '[]"]');
    let val;

    if ($prop.length) {
      val = [];
    } else {
      $prop = $(selector).filter(function () {
        let $this = $(this);
        if ($this.attr('type') === 'hidden') {
          return false;
        }
        return $this.data('papi-rule-value') || $(this).val() !== '';
      });

      if (!$prop.length) {
        $prop = $(selector).filter(function () {
          let $this = $(this);
          return $this.attr('type') === 'hidden' ||
            $this.data('papi-rule-value') ||
            $(this).val() !== '';
        });
      }
    }

    if (!$prop.length) {
      return;
    }

    $prop.each(function () {
      let $this = $(this);

      switch ($this.attr('type')) {
        case 'checkbox':
        case 'radio':
          if (!$this.is(':checked')) {
            return;
          }
          break;
        default:
          break;
      }

      if (val == null || val instanceof Array) {
        let prv = $this.data('papi-rule-value');
        if (prv !== undefined) {
          if (val instanceof Array) {
            val.push(prv);
          } else {
            val = prv;
          }
        } else {
          let v = $this.val();
          if (v !== '') {
            if (val instanceof Array) {
              val.push(v);
            } else {
              val = v;
            }
          }
        }
      }
    });

    return val;
  }

  /**
   * Setup rules.
   *
   * @param {object} $this
   */
  setupRules($this) {
    const rules = $.parseJSON($this.text());
    let slug    = $this.data('papi-rule-source-slug');

    for (let key in rules) {
      let rule = rules[key];

      if ($.type(rule) !== 'object') {
        continue;
      }

      this.bindRule(slug, rule, rules);
    }

    $this.removeAttr('data-papi-rules');
  }
}

export default Rules;
