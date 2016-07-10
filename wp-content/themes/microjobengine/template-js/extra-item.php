<script type="text/template" id="extra-item-loop">
    <div class="form-group list-item-extra">
        <div class="packge-chose">
            <div class="checkbox">
                <label>
                    <input data-id="{{= ID}}" data-featured="0" type="checkbox" value="{{= et_budget}}" name="mjob_extra">
                    <span>{{= post_title}}</span>
                    <div class="extra-content">{{= post_content }}</div>
                </label>
            </div>
        </div>
        <div class="package-price">
            {{= et_budget_text }}
        </div>
    </div>
</script>