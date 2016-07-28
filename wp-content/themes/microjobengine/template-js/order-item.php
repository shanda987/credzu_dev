<script type="text/template" id="order-item-loop">
    <h2><a href="{{= permalink }}">{{= post_title }}</a></h2>
    <div class="label-status {{= status_class }}">
        <span>{{= status_text }}</span>
    </div>
    <p><?php _e('Author ', ET_DOMAIN);?><span class="author-name">{{= mjob_author_name }}</span></p>
    <span class="date-post">{{= post_date }}</span>
</script>