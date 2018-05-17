<h4>@lang('frontend.toc.toctitle')</h4>
<textarea class="form-control" style="height: 150px; border: 1px #ccc solid; background-color: transparent" readonly>
@foreach (trans('frontend.toc.toctext') as $line)
	{{ str_replace(':appname', config('app.name'), $line) }}
@endforeach
</textarea>
