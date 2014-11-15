(function () {

  // Post view for property post

  papi.view.Post = wp.Backbone.View.extend({

    // The template to compile
    template: wp.template('papi-post'),

    // Render post template with the given data object.
    render: function (data) {
      var template = _.template(this.template()),
          html = template(data);

      this.$el.append(html);
    }

  });

}());
