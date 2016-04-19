<script type="text/template" id="task-item-loop">
    <h2><a href="{{= permalink }}">{{= post_title }}</a></h2>
    <div class="label-status {{= status_class }}">
        <span>{{= status_text }}</span>
    </div>
    <p><?php _e('Order by ', ET_DOMAIN);?><a href="{{= mjob_order_author_url }}">{{= author_name }}</a></p>
    <span class="date-post">{{= post_date }}</span>
</script>