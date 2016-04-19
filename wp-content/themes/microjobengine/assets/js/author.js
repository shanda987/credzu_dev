(function(AE, Views, Models, Collections, $) {
    $(document).ready(function() {
        Views.Author = Backbone.View.extend({
            el: '.mjob-author-page',
            events: {
                'click .show-bio': 'showHiddenBio'
            },
            initialize: function() {

            },
            showHiddenBio: function(event) {
                event.preventDefault();
                var showBio = $('.show-bio');
                $('.hidden-bio').toggleClass('show');
                $('.hidden-bio').toggleClass('gradient');
                if(showBio.hasClass('hide-bio')) {
                    showBio.text(ae_globals.show_bio_text);
                    showBio.removeClass('hide-bio');
                } else {
                    showBio.text(ae_globals.hide_bio_text);
                    showBio.addClass('hide-bio');
                }
            }
        });
        new Views.Author();

        var mjobItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'clearfix',
            template: _.template($('#mjob-list-item').html()),
            initialize: function() {
                this.renderRating();
            },
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                var view = this;
                if( view.$el.find('input[name="_wpnonce"]').length > 0 ){
                    view.model.set('_wpnonce', view.$el.find('input[name="_wpnonce"]').val());
                }
                this.renderRating();
            },
            // Render rating star
            renderRating: function() {
                var view = this;
                this.$el.find('.rate-it').raty({
                    readOnly: true,
                    half: true,
                    score: function() {
                        return view.model.get('rating_score');
                    },
                    hints: raty.hint
                });
            }
        });

        // Block control initialize
        var $container = $('.mjob-author-page');
        if($container.length > 0) {
            if(typeof mjobCollection === "undefined") {
                if($('.mjob_postdata').length > 0) {
                    var data = JSON.parse($('.mjob_postdata').html());
                    mjobCollection = new Collections.Mjob(data);
                } else {
                    mjobCollection = new Collections.Mjob();
                }

                new Views.ListPost({
                    el: $container.find('.list-job ul'),
                    itemView: mjobItem,
                    collection: mjobCollection
                });

                new Views.BlockControl({
                    el: $container,
                    collection: mjobCollection,
                    thumbnail: "medium_post_thumbnail"
                })
            }
        }
    });
})(window.AE, window.AE.Views, window.AE.Models, window.AE.Collections, jQuery);