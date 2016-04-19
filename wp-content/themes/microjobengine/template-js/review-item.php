<script type="text/template" id="review-item-loop">
    <div class="image-avatar">
        {{= avatar_user }}
    </div>
    <div class="profile-viewer">
        <a href="{{= author_data.author_url }}" class="name-author">
            {{= author_data.display_name }}
        </a>
        <p class="review-time">{{= date_ago }}</p>
        <div class="rate-it star" data-score="{{= et_rate }}"></div>
        <div class="comment-content">{{= comment_content }}</div>
    </div>
</script>