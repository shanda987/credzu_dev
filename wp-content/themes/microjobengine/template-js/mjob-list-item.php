<script type="text/template" id="mjob-list-item">
    <div class="image-avatar">
        <a href="{{= permalink }}">
            <img src="{{= the_post_thumbnail }}" alt="">
        </a>
    </div>
    <div class="info-items">
        <h2><a href="{{= permalink }}">{{= post_title }}</a></h2>
        <div class="group-function">
            <div class="vote">
                <div class="rate-it star" data-score="{{= rating_score }}"></div>
                <span class="total-review">({{= mjob_total_reviews }})</span>
            </div>
            <span class="price">{{= et_budget_text }}</span>
        </div>
    </div>
</script>