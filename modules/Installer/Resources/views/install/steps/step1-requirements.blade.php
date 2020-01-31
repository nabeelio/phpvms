@extends('installer::app')
@section('title', 'Requirements Check')

@section('content')
  <div style="align-content: center;">
    {{ Form::open(['route' => 'installer.step2', 'method' => 'GET']) }}
    <table class="table" width="25%">
      <tr>
        <td colspan="2"><h4>php version</h4></td>
      </tr>
      <tr>
        <td>PHP Version: {{ $php['version'] }}</td>
        <td style="text-align:center;">
          @if($php['passed'] === true)
            <span class="badge badge-success">OK!</span>
          @else
            <span class="badge badge-danger">Failed!</span>
          @endif
        </td>
      </tr>

      <tr>
        <td colspan="2"><h4>php extensions</h4></td>
      </tr>
      @foreach($extensions as $ext)
        <tr>
          <td>{{ $ext['ext'] }}</td>
          <td style="text-align:center;">
            @if($ext['passed'] === true)
              <span class="badge badge-success">OK!</span>
            @else
              <span class="badge badge-danger">Failed!</span>
            @endif
          </td>
        </tr>
      @endforeach

      <tr>
        <td colspan="2">
          <h4>directory permissions</h4>
          <p>Make sure these directories have read and write permissions</p>
        </td>
      </tr>
      @foreach($directories as $dir)
        <tr>
          <td>{{ $dir['dir'] }}</td>
          <td style="text-align:center;">
            @if($dir['passed'] === true)
              <span class="badge badge-success">OK!</span>
            @else
              <span class="badge badge-danger">Failed!</span>
            @endif
          </td>
        </tr>
      @endforeach
    </table>
    @if($passed === true)
      <p style="text-align: right">
        {{ Form::submit('Database Setup >>', ['class' => 'btn btn-success']) }}
      </p>
    @endif
    {{--{{ $php_version }}
    {{ $extensions }}
    {{ $passed }}--}}
    {{ Form::close() }}
  </div>
@endsection
