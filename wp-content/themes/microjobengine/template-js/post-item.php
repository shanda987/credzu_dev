<script type="text/template" id="ae-post-loop">
    <div class="image-avatar">
        <a href="{{= permalink }}">
            <img src="{{= the_post_thumbnail }}" alt="">
        </a>
    </div>
    <div class="info-items">
        <h2><a href="{{= permalink  }}">{{= post_title }}</a></h2>
        <div class="group-function">
            {{= post_excerpt }}
            {{= comment_number }}
            <#if( comment_number > 1){ #>
                <?php _e('Comments', ET_DOMAIN) ?>
            <# } else{ #>
                <?php _e('Comment', ET_DOMAIN); ?>
            <# } #>
        </div>
    </div>
    <div class="image-avatar col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <a href="{{= permalink }}">
            <img src="{{= the_post_thumbnail }}" alt="">
        </a>
    </div>
    <div class="info-items col-lg-8 col-md-8 col-sm-12 col-xs-12">
        <p class="author-post"><?php _e('Written by ', ET_DOMAIN);?>{{= author_name }}</p>
        <p class="date-post">{{= post_date }}</p>
        <h2><a href="{{= permalink }}">{{= post_title }}</a></h2>
        <div class="group-function">
            {{= post_excerpt }}
            <a href="{{= permalink }}" class="more"><?php _e('Read more', ET_DOMAIN); ?></a>
            <p class="total-comments">
                {{= comment_number }}
            </p>
        </div>
    </div>
</script>