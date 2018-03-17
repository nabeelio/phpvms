{{--
DO NOT MODIFY THIS FILE. THINGS WILL BREAK IF YOU DO
--}}
<script>
@if (Auth::user())
    const PHPVMS_USER_API_KEY = "{!! Auth::user()->api_key !!}";
@else
    const PHPVMS_USER_API_KEY = false;
@endif
</script>
