<script type="text/template" id="history-item-loop">
    <td>{{= payment_method_text }}</td>
    <td>{{= history_time }}</td>
    <td>{{= amount_text }}</td>
    <td class="<# if(history_status == 'completed') { #> successful <# } else if(history_status == 'cancelled') { #> rejected <# } else { #> pending <# } #>">{{= history_status_text }}</td>
</script>