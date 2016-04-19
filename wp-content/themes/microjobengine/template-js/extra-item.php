<script type="text/template" id="extra-item-loop">
    <div class="form-group list-item-extra">
        <div class="packge-chose">
            <div class="checkbox">
                <label>
                    <input data-id="{{= ID}}" type="checkbox" value="{{= et_budget}}" name="mjob_extra">
                    <span>{{= post_title}}</span>
                </label>
            </div>
        </div>
        <div class="package-price">
            {{= et_budget_text }}
        </div>
    </div>
</script>