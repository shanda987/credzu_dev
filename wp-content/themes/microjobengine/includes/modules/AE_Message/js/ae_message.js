(function($, Models, Collections, Views) {
	$(document).ready(function() {
		Models.Message = Backbone.Model.extend({
			action: 'ae-ae_message-sync',
			defaults:{
				post_type:'ae_message'
			},
			initialize: function() {}
		});
		/**
		 * mjob collections
		 */
		Collections.Message = Backbone.Collection.extend({
			model: Models.Message,
			action: 'ae-fetch-ae_message',
			initialize: function() {
				this.paged = 1;
			}
		});
	});
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
