(function (wp, _) {

  'use strict';

  // Image view for property image
  papi.views.Image = wp.Backbone.View.extend({

    // The template to compile
    template: wp.template('papi-image'),

    // Render image template with the given data object.
    render: function (data) {
      var template = _.template(this.template());
      var html = template(data);

      this.$el.append('<div class="attachment">' + html + '</div>');
    }

  });

}(window.wp, window._));
