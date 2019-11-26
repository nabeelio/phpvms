@extends('installer::app')
@section('title', 'Import Configuration')

@section('content')
  <div style="align-content: center;">
    {{ Form::open(['route' => 'importer.config', 'method' => 'POST']) }}
    <table class="table" width="25%">

      <tr>
        <td colspan="2"><h4>Site Config</h4></td>
      </tr>

      <tr>
        <td>Admin Email</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'email', '', ['class' => 'form-control']) }}
            <p>The admin's email address, the password for this will be reset</p>
          </div>
        </td>
      </tr>

      <tr>
        <td colspan="2"><h4>Database Config</h4></td>
      </tr>

      <tbody id="mysql_settings">
      <tr>
        <td>Database Host</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'db_host', '127.0.0.1', ['class' => 'form-control']) }}
          </div>
        </td>
      </tr>

      <tr>
        <td>Database Port</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'db_port', '3306', ['class' => 'form-control']) }}
          </div>
        </td>
      </tr>

      <tr>
        <td>Database Name</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'db_name', 'phpvms', ['class' => 'form-control']) }}
          </div>
        </td>
      </tr>

      <tr>
        <td>Database User</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'db_user', null, ['class' => 'form-control']) }}
          </div>
        </td>
      </tr>

      <tr>
        <td>Database Password</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'db_pass', null, ['class' => 'form-control']) }}
          </div>
        </td>
      </tr>

      <tr>
        <td colspan="2" style="text-align: right;">
          {{ Form::submit('Test Database Credentials', ['class' => 'btn btn-info', 'id' => 'dbtest_button']) }}
        </td>
      </tr>
      </tbody>

      <tr>
        <td>Database Prefix</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'db_prefix', 'phpvms_', ['class' => 'form-control']) }}
            <p>Prefix of the tables, if you're using one</p>
          </div>
        </td>
      </tr>

    </table>
    <div id="dbtest"></div>
    <p style="text-align: right">
      {{ Form::submit('Start Importer >>', ['class' => 'btn btn-success']) }}
    </p>
    {{ Form::close() }}
  </div>
@endsection

@section('scripts')
  <script>
    $(document).ready(() => {

      $("#dbtest_button").click((e) => {
        e.preventDefault();
        const opts = {
          _token: "{{ csrf_token() }}",
          db_conn: $("#db_conn option:selected").text(),
          db_host: $("input[name=db_host]").val(),
          db_port: $("input[name=db_port]").val(),
          db_name: $("input[name=db_name]").val(),
          db_user: $("input[name=db_user]").val(),
          db_pass: $("input[name=db_pass]").val(),
        };

        $.post("{{ route('installer.dbtest') }}", opts, (data) => {
          $("#dbtest").html(data);
        })
      })
    });
  </script>
@endsection
