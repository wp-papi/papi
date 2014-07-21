(function () {

  // Image view for property image
  ptb.view.Image = wp.Backbone.View.extend({

    // The template to compile
    template: wp.template('ptb-image'),

    tagName: 'img',

    events: {
      'mouseover': 'hover'
    },

    initialize: function (options) {
      this.options = options || {
        li: false
      };
    },

    // Render image template with the given data object.
    render: function (data) {
      var template = _.template(this.template()),
          html = template(data);

      if (this.options.li) {
        template = $('<li />').html(html);
      }
      this.$el.append(html);
    },

    hover: function () {
      console.log('mouse hover')
    }

  });

}());