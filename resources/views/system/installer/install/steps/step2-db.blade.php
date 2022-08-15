@extends('system.installer.app')
@section('title', 'Database Setup')
@section('content')
  <div style="align-content: center;">
    {{ Form::open(['route' => 'installer.envsetup', 'method' => 'POST']) }}
    <table class="table" width="25%">

      <tr>
        <td colspan="2"><h4>Site Config</h4></td>
      </tr>

      <tr>
        <td>Site Name</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'site_name', 'phpvms', ['class' => 'form-control']) }}
          </div>
        </td>
      </tr>

      <tr>
        <td>Site URL</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'app_url', Request::root(), ['class' => 'form-control']) }}
          </div>
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <h4>Database Config</h4>
          <p>Enter the target database information</p>
        </td>
      </tr>

      <tr>
        <td><p>Database Type</p></td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::select('db_conn', $db_types, null, ['class' => 'form-control', 'id' => 'db_conn']) }}
          </div>
        </td>
      </tr>

      <tbody id="mysql_settings" class="settings_panel">
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

      <tbody id="sqlite_settings" class="settings_panel">

      </tbody>

      <tr>
        <td>Database Prefix</td>
        <td style="text-align:center;">
          <div class="form-group">
            {{ Form::input('text', 'db_prefix', '', ['class' => 'form-control']) }}
            <p>Set this if you're sharing the database with another application.</p>
          </div>
        </td>
      </tr>

    </table>
    <div id="dbtest"></div>
    <p style="text-align: right">
      {{ Form::submit('Setup Database >>', ['class' => 'btn btn-success']) }}
    </p>
    {{ Form::close() }}
  </div>
@endsection

@section('scripts')
  <script>
    function changeForm(selected) {
      $("tbody.settings_panel").hide();
      $("tbody#" + selected + "_settings").show();
    }

    $(document).ready(() => {

      const selValue = $("#db_conn option:selected").text();
      changeForm(selValue);

      $("#db_conn").change((e) => {
        const selValue = $("#db_conn option:selected").text();
        changeForm(selValue);
      });

      $("#dbtest_button").click((e) => {
        e.preventDefault();
        const opts = {
          method: 'POST',
          url: '/install/dbtest',
          data: {
            _token: "{{ csrf_token() }}",
            db_conn: 'mysql',
            db_host: $("input[name=db_host]").val(),
            db_port: $("input[name=db_port]").val(),
            db_name: $("input[name=db_name]").val(),
            db_user: $("input[name=db_user]").val(),
            db_pass: $("input[name=db_pass]").val(),
          },
        };

        phpvms.request(opts).then(response => {
          $("#dbtest").html(response.data);
        });
      })
    });
  </script>
@endsection
