@extends('importer::app')
@section('title', 'Import Configuration')

@section('content')
  <div style="align-content: center;">
    {{ Form::open(['route' => 'importer.config', 'method' => 'POST']) }}
    <table class="table">
      <tr>
        <td colspan="2">
          <h4>IMPORTANT NOTES</h4>
          <ul>
            <li>The first user's password (admin) will be "admin". Please change it after logging in</li>
            <li>User passwords will be reset and they will need to use "Forgot Password" to reset it</li>
            <li>If you have more than 1000 PIREPs or flights, it's best to use the command-line importer!
              <a href="http://docs.phpvms.net/setup/importing-from-v2-v5" target="_blank">Click here</a> to
              see the documentation of how to use it.
            </li>
            <li><strong>THIS WILL WIPE OUT YOUR EXISTING DATA</strong> - this is required to make sure that things like
              pilot IDs match up
            </li>
          </ul>
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <h4>Database Config</h4>
          <p>Enter the database information for your legacy phpVMS installation</p>
        </td>
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
          db_conn: 'mysql',
          db_host: $("input[name=db_host]").val(),
          db_port: $("input[name=db_port]").val(),
          db_name: $("input[name=db_name]").val(),
          db_user: $("input[name=db_user]").val(),
          db_pass: $("input[name=db_pass]").val(),
        };

        $.post("{{ route('importer.dbtest') }}", opts, (data) => {
          $("#dbtest").html(data);
        })
      })
    });
  </script>
@endsection
