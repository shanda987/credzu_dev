(function($, Views, Models, Collections, AE) {
    $(document).ready(function() {
        Models.mJobConversation = Backbone.Model.extend({
            action: 'mjob_conversation_sync'
        });

        /**
         * CONVERSATION ITEM VIEW
         */
        var conversationItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'clearfix conversation-item',
            template: _.template($('#conversation-item-loop').html())
        });

        Views.ConversationList = Views.ListPost.extend({
            tagName: 'ul',
            itemView: conversationItem,
            itemClass: 'history-item'
        });

        var conversationContainer = $('.mjob_conversation_list_page');
        if(conversationContainer.length > 0) {
            if(typeof conversationCollection === "undefined") {
                if($('.conversation_postdata').length > 0) {
                    var conversation = JSON.parse($('.conversation_postdata').html());
                    conversationCollection = new Collections.Message(conversation);
                } else {
                    conversationCollection = new Collections.Message();
                }
            }
            // Conversation list view
            var conversationList = new Views.ConversationList({
                itemView: conversationItem,
                collection: conversationCollection,
                el: conversationContainer.find('.list-conversation')
            });

            // Conversation block control
            new Views.BlockControl({
                collection: conversationCollection,
                el: conversationContainer,
            })
        }

        /**
         * BLOCK CONTROL FOR MESSAGES LIST IN CONVERSATION DETAIL
         */
        var messageItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'clearfix message-item',
            template: _.template($('#message-item-loop').html())
        });

        Views.MessageList = Views.ListPost.extend({
            tagName: 'ul',
            itemView: messageItem,
            itemClass: 'message-item',
            initialize: function(options) {
                _.extend(this, options);
                Views.ListPost.prototype.initialize.call(this, options);
            },
        });

        var messageContainer = $('.mjob_conversation_detail_page');
        if(messageContainer.length > 0) {
            if(typeof messageCollection === "undefined") {
                if($('.message_postdata').length > 0) {
                    var messages = JSON.parse($('.message_postdata').html());
                    messageCollection = new Collections.Message(messages);
                } else {
                    messageCollection = new Collections.Message();
                }
            }
            // Message list view
            var messageList = new Views.MessageList({
                itemView: messageItem,
                collection: messageCollection,
                el: messageContainer.find('.list-conversation'),
                appendHtml: function(collectionView, itemView, index){
                    collectionView.$el.prepend(itemView.el);
                }
            });
            // Message block control
            new Views.BlockControl({
                collection: messageCollection,
                el: messageContainer,

            })
        }

        /**
         * CONVERSATION MODAL
         */
        Views.ModalConversation = Views.Modal_Box.extend({
            el: '#conversation',
            events: {

            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                if(typeof this.model === 'undefined') {
                    this.model = new Models.Message();
                }

                AE.pubsub.on('ae:form:submit:success', this.sendMessageSuccess, this);
            },
            onOpen: function(data) {
                var view = this;
                view.model.set('type', 'conversation');
                view.model.set('from_user', data.from_user);
                view.model.set('to_user', data.to_user);
                view.model.set('is_conversation', 1);
                view.model.set('conversation_status', 'unread');
                view.model.set('post_title', conversation_global.conversation_title);
                view.setupFields();
                view.openModal();
            },
            setupFields: function() {
                var view = this;

                if (typeof view.carousels === 'undefined') {
                    view.carousels = new Views.Carousel({
                        el: $('.gallery_container_modal_conversation'),
                        uploaderID:'carousel_modal_conversation',
                        model: view.model,
                        carouselTemplate: '#ae_carousel_file_template',
                        extensions: ae_globals.file_types
                    });
                }

                view.conversationForm = new Views.AE_Form({
                    el: '.mjob-modal-conversation-form', // Wrapper of form
                    model: this.model,
                    rules: {
                        conversation_content: 'required'
                    },
                    type: 'conversation',
                    blockTarget: '.mjob-modal-conversation-form button'
                })
            },
            sendMessageSuccess: function(result, resp, jqXHR, type) {
                if(type == 'conversation') {
                    var view = this;
                    if(resp.success == true) {
                        // Update contact link
                        $('.contact-link').removeClass('do-contact');
                        $('.contact-link').attr('href', resp.data.permalink);

                        // Reset form
                        view.$el.find('#post_content').val('');
                        view.$el.find('.gallery-image').html('');

                        window.location.href = resp.data.permalink;
                    }
                    view.closeModal();
                }
            }
        });

        Views.Conversation = Backbone.View.extend({
            el: 'body',
            events: {
                'click .do-contact' : 'doContact',
                'click .mark-as-read' : 'doMarkAsRead'
            },
            initialize: function() {
                if($('#current_user').length > 0) {
                    if(typeof currentUser.data !== "undefined") {
                        this.user = new Models.mJobUser(currentUser.data);
                    } else {
                        this.user = new Models.mJobUser(currentUser);
                    }
                } else {
                    this.user = new Models.mJobUser();
                }

                this.model = new Models.Message();
                this.conversationObj = new Models.mJobConversation();
            },
            doContact: function(event) {
                event.preventDefault();
                var toUser = $(event.currentTarget).attr('data-touser');

                // Check if user logged in or not
                if(this.user.get('id') == 0 || this.user.get('id') == "") {
                    // Open sign in modal
                    if(typeof this.signInModal === "undefined") {
                        this.signInModal = new Views.SignInModal();
                    }
                    this.signInModal.openModal();
                } else if(this.user.get('id') != toUser) {
                   if(typeof currentUser.data.register_status !== 'undefined' && currentUser.data.register_status == '') {
                       // Open conversation modal
                       if(typeof this.conversationModal === "undefined") {
                           this.conversationModal = new Views.ModalConversation();
                       }
                       this.conversationModal.onOpen({
                           'to_user': toUser,
                           'from_user': this.user.get('ID')
                       });
                   } else {
                       AE.pubsub.trigger('ae:notification', {
                           notice_type: 'error',
                           msg: ae_globals.pending_account_error_txt
                       })
                   }
                }

                // Update auhentication form redirect
                var current_url = window.location.href;
                $('#signInForm .redirect_url').val(current_url);
                $('#signUpForm .redirect_url').val(current_url);
            },
            doMarkAsRead: function(event) {
                event.preventDefault();
                var view = this;
                view.conversationObj.set('do_action', 'mark_as_read');
                view.conversationObj.save('', '', {
                    success: function(status, res, jqXHR) {
                        if(res.success == true) {
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'success',
                                msg: res.msg
                            })
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'error',
                                msg: res.msg
                            })
                        }
                    }
                })
            }
        });

        new Views.Conversation();

        /**
         * CONVERSATION VIEW IN SINGLE
         */
        Views.SingleConversation = Backbone.View.extend({
            el: '.mjob_conversation_detail_page',
            initialize: function() {
                this.initModel();
                this.initSendMessageForm();
                this.initCarousel();

                if($('#default-message-query').length > 0) {
                    this.query = JSON.parse($('#default-message-query').html());
                } else {
                    this.query = {};
                }

                // Init list message collection and view
                var messageContainer = $('.mjob_conversation_detail_page');
                if(typeof this.messageCollection === "undefined") {
                    if($('.message_postdata').length > 0) {
                        var messages = JSON.parse($('.message_postdata').html());
                        this.messageCollection = new Collections.Message(messages);
                    } else {
                        this.messageCollection = new Collections.Message();
                    }
                }
                // Message list view
                this.messageList = new Views.MessageList({
                    itemView: messageItem,
                    collection: this.messageCollection,
                    el: messageContainer.find('.list-conversation'),
                });
                AE.pubsub.on('ae:form:submit:success', this.sendMessageSuccess, this);
            },
            // Init new model
            initModel: function() {
                this.model = new Models.Message();

                // Initialize model
                this.model.set('type', 'message');
                this.model.set('post_parent', $('#conversation_id').val());
                this.model.set('from_user', $('#from_user').val());
                this.model.set('to_user', $('#to_user').val());
                this.model.set('post_title', conversation_global.message_title);
            },
            // Init new form
            initSendMessageForm: function() {
                if(typeof this.conversationForm === "undefined") {
                    this.conversationForm = new Views.AE_Form({
                        el: '.mjob-conversation-form', // Wrapper of form
                        model: this.model,
                        rules: {
                            post_content: 'required'
                        },
                        type: 'conversation-single',
                        blockTarget: '.mjob-conversation-form button'
                    });
                }
            },
            // Init new carousel
            initCarousel: function() {
                if(typeof this.carousel === "undefined") {
                    this.carousel = new Views.Carousel({
                        el: $('.gallery_container_single_conversation'),
                        uploaderID:'carousel_single_conversation',
                        model: this.model,
                        carouselTemplate: '#ae_carousel_file_template',
                        extensions: ae_globals.file_types
                    });
                }
            },
            // Trigger send message success
            sendMessageSuccess: function(result, resp, jqXHR, type) {
                if(type == 'conversation-single') {
                    var view = this;
                    if( $('.list-conversation').find('li').length == 0 ){
                        $('.list-conversation').html('');
                    }
                    if(resp.success == true) {
                        view.messageCollection.fetch({
                            data: {
                                query: view.query,
                                page: 1,
                                paged: 1,
                                paginate: true,
                            }
                        });

                        // Reset model
                        view.initModel();
                        view.carousel.setModel(this.model);
                        view.carousel.setupView();
                        view.conversationForm.resetModel(this.model);

                        // Reset form
                        view.$el.find('#post_content').val('');
                    }
                }
            }
        });

        new Views.SingleConversation();
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections, window.AE);