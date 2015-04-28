import $ from 'jquery';
import * as Papi from 'papi/main';
import * as Properties from 'papi-properties/main';

// Extend Papi object.
$.extend(window.papi || {}, {
  properties: {},
  views: {}
});

// Initialize all packages.
Papi.init();
Properties.init();
