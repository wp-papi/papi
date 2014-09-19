(function () {

  // Image view for property image

  papi.view.Image = wp.Backbone.View.extend({

    // The template to compile
    template: wp.template('papi-image'),

    // Render image template with the given data object.
    render: function (data) {
      var template = _.template(this.template()),
          html = template(data);

      this.$el.append('<li>' + html + '</li>');
    }

  });

}());
