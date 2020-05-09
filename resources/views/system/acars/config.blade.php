@php
echo '<?xml version = "1.0" encoding = "utf-8"?>';
@endphp
<AcarsConfiguration xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.phpvms.net/acars/config">
  <AirlineUrl>{{ config('app.url') }}</AirlineUrl>
  <ApiKey>{{ $user->api_key }}</ApiKey>
</AcarsConfiguration>
