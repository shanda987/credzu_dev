(function($, Models, Collections, Views) {
    $(document).ready(function() {
        /**
         * model withdraw
         */
        Models.mJobOrder = Backbone.Model.extend({
            action: 'mjob-admin-order-sync',
            initialize: function() {}
        });
        Collections.mJobOrder = Backbone.Collection.extend({
            model: Models.mJobOrder,
            action: 'mjob-admin-fetch-order',
            initialize: function() {
                this.paged = 1;
            }
        });
        var mJobOrderItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'mjob-order-item',
            template: _.template($('#mjob-order-loop').html()),
            onItemBeforeRender: function() {
                // before render view
                // console.log('render');
            },
            onItemRendered: function() {
                // after render view
            }
        });
        ListMjobOrders = Views.ListPost.extend({
            tagName: 'li',
            itemView: mJobOrderItem,
            itemClass: 'mjob-order-item'
        });
        // notification list control
        if( $('.mjob-order-container').length > 0 ){

            if( $('.mjob-order-container').find('.mjob_order_data').length > 0 ){
                var postsdata = JSON.parse($('.mjob-order-container').find('.mjob_order_data').html()),
                    posts = new Collections.mJobOrder(postsdata);
            } else {
                var posts = new Collections.mJobOrder();
            }

            /**
             * init list blog view
             */
            new ListMjobOrders({
                itemView: mJobOrderItem,
                collection: posts,
                el: $('.mjob-order-container').find('.list-mjob-orders')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: posts,
                el: $('.mjob-order-container'),
            });

            /**
             * Decline withdraw
             */
                // Decline modal
                //Views.DeclineWithdrawModal = Views.Modal_Box.extend({
                //	el: '#decline-withdraw-modal',
                //	events: {
                //		'submit form' : 'doDecline'
                //	},
                //	initialize: function() {
                //
                //	},
                //	doDecline: function(event) {
                //		event.preventDefault();
                //	}
                //});

                // Withdraw container view
            Views.mJobOrderContainer = Backbone.View.extend({
                el: '.mjob-order-container',
                initialize: function() {
                    AE.pubsub.on('ae:model:ondecline-mjob-order', this.declineMjobOrder, this);
                },

                declineMjobOrder: function(model) {
                    this.model = model;
                    var message = prompt("Please give a decline reason", "");
                    // remove white spaces
                    var compareStr = message.replace(/\s+/g, '');
                    // remove break lines
                    compareStr = message.replace(/(\r\n|\n|\r)/gm,"");
                    if(compareStr != "") {
                        this.model.set('reject_message', message);
                        this.model.save('post_status', 'draft', {
                            beforeSend: function() {

                            },
                            success: function(status, resp, jqXHR) {
                                if(resp.success == true) {

                                } else {

                                }
                            }
                        })
                    }
                }
            });
            new Views.mJobOrderContainer();
        }
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);