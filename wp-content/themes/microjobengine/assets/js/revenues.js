(function($, Views, Models, Collections, AE) {
    $(document).ready(function() {
        Models.Revenue = Backbone.Model.extend({
            action: 'mjob_revenue_sync'
        });

        Models.Withdraw = Backbone.Model.extend({
            action: 'mjob_withdraw_sync'
        });

        Views.Revenues = Backbone.View.extend({
            el: '.mjob-revenues-page',
            events: {
                'click .request-secure-code' : 'requestSecureCode'
            },
            initialize: function() {
                // Init model
                if($('#current_user').length > 0) {
                    this.user = new Models.mJobUser(currentUser.data);
                } else {
                    this.user = new Models.mJobUser();
                }

                this.revenue = new Models.Revenue();

                this.withdraw = new Models.Withdraw();

                // Init block ui
                this.blockUi = new Views.BlockUi();

                var view = this;

                // Form control
                var bankAccountForm = new Views.AE_Form({
                    el: '#withdrawForm',
                    model: view.withdraw,
                    rules: {
                        amount: 'required',
                        secure_code: 'required',
                    },
                    type: 'withdrawMoney',
                    blockTarget: '#withdrawForm button'
                });

                AE.pubsub.on('ae:form:submit:success', this.withdrawSuccess, this);
            },
            requestSecureCode: function(event) {
                event.preventDefault();
                var view = this;
                var target = $(event.currentTarget);
                view.revenue.set({
                    'do_action': 'request_secure_code',
                    '_wpnonce': $('#_wpnonce').val()
                })
                view.revenue.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block(target);
                    },
                    success: function(status, resp, jqXHR) {
                        if(resp.success == true) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: resp.msg,
                                notice_type: 'success'
                            })
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: resp.msg,
                                notice_type: 'error'
                            })
                        }

                        view.blockUi.unblock();
                    }
                })
            },
            withdrawSuccess: function(result, resp, jqXHR, type) {
                var view = this;
                if(resp.success == true) {
                    view.$el.find('#withdrawForm #amount').val('');
                    view.$el.find('#withdrawForm #secure_code').val('');
                }

                // Update revenues
                if(typeof resp.data !== "undefined") {
                    $('.revenues .withdrew-text').text(resp.data.withdrew_text);
                    $('.revenues .freezable-text').text(resp.data.freezable_text);
                    $('.revenues .available-text').text(resp.data.available_text);
                }

            }
        });

        new Views.Revenues();

        /**
         * HISTORY
         */
        Models.History = Backbone.Model.extend({
            action: 'mjob_history_sync'
        });

        Collections.History = Backbone.Collection.extend({
            model: Models.History,
            action: 'fre-fetch-history',
            initialize: function() {
                this.paged = 1;
            }
        });

        var historyItem = Views.PostItem.extend({
            tagName: 'tr',
            className: 'history-item',
            template: _.template($('#history-item-loop').html()),
            initialize: function() {
            },
            onItemBeforeRender: function() {

            },
            onItemRendered: function() {

            },
        });

        //var historyItem = new Views.HistoryItem();

        Views.HistoryList = Views.ListPost.extend({
            tagName: 'table',
            itemView: historyItem,
            itemClass: 'history-item'
        });

        var historyContainer = $('.mjob-withdraw-history-container');
        if(historyContainer.length > 0) {
            if(typeof historyCollection === 'undefined') {
                if ($('.withdraw_history_postdata').length > 0) {
                    var history = JSON.parse($('.withdraw_history_postdata').html());
                    historyCollection = new Collections.History(history);
                } else {
                    historyCollection = new Collections.History();
                }
            }

            var listHistories = new Views.HistoryList({
                itemView: historyItem,
                collection: historyCollection,
                el: historyContainer.find('.list-histories')
            });

            new Views.BlockControl({
                collection: historyCollection,
                el: historyContainer,
                onAfterLoadMore: function(result, res){
                    //console.log(res);
                }
            });
        }
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections, window.AE);