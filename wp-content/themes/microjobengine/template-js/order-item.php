<script type="text/template" id="order-item-loop">
    <h2><a href="{{= permalink }}">{{= post_title }}</a></h2>
    <div class="label-status {{= status_class }}">
        <span>{{= status_text }}</span>
    </div>
    <p><?php _e('Author ', ET_DOMAIN);?><a href="{{= mjob_author_url }}">{{= mjob_author_name }}</a></p>
    <span class="date-post">{{= post_date }}</span>
</script>