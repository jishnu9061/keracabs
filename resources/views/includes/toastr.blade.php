
<script>
    $(function() {
            @if(Session::has('appNotificaion'))
        var level = "{{ Session::get('appNotificaion')['level'] }}";
        var message = "{{ Session::get('appNotificaion')['message'] }}";
        @php Session::forget('appNotificaion'); @endphp
            switch (level) {
            case 'success' :
                toastr.success(message);
                break;
            case 'error' :
                toastr.error(message);
                break;
            case 'notify' :
                toastr.info(message);
                break;
            case 'warning' :
                toastr.warning(message);
                break;
            default :
                toastr.info(message);
                break;
        }
        @endif
    });
</script>

