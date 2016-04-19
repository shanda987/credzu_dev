(function($, Views, Models, Collections) {

    $(document).ready(function() {

        /*Scroll slider Categories*/
        $("#owl").owlCarousel({
            items : 4,
            itemsDesktop : [1170,3],
            itemsDesktopSmall : [1170,3],
            autoPlay:3000,
        });

        Views.PostJob = Backbone.View.extend({
            el: 'body',
            events: {

            },
            initialize: function() {

            },

        })
    });

})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections)