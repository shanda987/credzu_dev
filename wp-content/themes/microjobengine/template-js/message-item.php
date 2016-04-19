<script type="text/template" id="message-item-loop">
    <div class="{{= message_class }}">
        <div class="img-avatar">
            {{= author_avatar}}
        </div>

        <div class="conversation-text">
            {{= post_content_filtered}}
            <ul>
                {{= message_attachment }}
            </ul>
        </div>

        <span class="message-time">
            {{= post_date }}
        </span>
    </div>
</script>