@if($errors->has($field))
  <p class="text-danger" style="margin-top: 10px;">{{ $errors->first($field) }}</p>
  {{--<div class="alert alert-danger" role="alert" style="margin-top: 10px;">
      {{ $errors->first($field) }}
  </div>--}}
@endif
