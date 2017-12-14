@extends('installer::app')
@section('title', 'Database Setup')
@section('content')
<div style="align-content: center;">
    {!! Form::open(['route' => 'installer.dbsetup', 'method' => 'POST']) !!}
    <table class="table" width="25%">
        <tr>
            <td>Select Database Type</td>
            <td style="text-align:center;">
                <div class="form-group">
                {!! Form::select('db_conn', $db_types, null, ['class' => 'form-control', 'id' => 'db_conn']) !!}
                </div>
            </td>
        </tr>

        <tbody id="mysql_settings" class="settings_panel">
            <tr>
                <td>Database Host</td>
                <td style="text-align:center;">
                    <div class="form-group">
                    {!! Form::input('text', 'db_host', null, ['class' => 'form-control']) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td>Database Port</td>
                <td style="text-align:center;">
                    <div class="form-group">
                        {!! Form::input('text', 'db_port', '3307', ['class' => 'form-control']) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td>Database Name</td>
                <td style="text-align:center;">
                    <div class="form-group">
                        {!! Form::input('text', 'db_name', 'phpvms', ['class' => 'form-control']) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td>Database User</td>
                <td style="text-align:center;">
                    <div class="form-group">
                        {!! Form::input('text', 'db_user', null, ['class' => 'form-control']) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td>Database Password</td>
                <td style="text-align:center;">
                    <div class="form-group">
                        {!! Form::input('text', 'db_pass', null, ['class' => 'form-control']) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: right;">
                    {!! Form::submit('Test Database Credentials', ['class' => 'btn btn-info', 'id' => 'dbtest_button']) !!}
                </td>
            </tr>
        </tbody>

        <tbody id="sqlite_settings" class="settings_panel">

        </tbody>

    </table>
    <div id="dbtest"></div>
    <p style="text-align: right">
        {!! Form::submit('Complete Setup >>', ['class' => 'btn btn-success']) !!}
    </p>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')
<script>
function changeForm(selected) {
    $("tbody.settings_panel").hide();
    $("tbody#" + selected + "_settings").show();
}

$(document).ready(function() {
    var selValue = $("#db_conn option:selected").text();
    changeForm(selValue);

    $("#db_conn").change(function(e) {
        var selValue = $("#db_conn option:selected").text();
        changeForm(selValue);
    });

    $("#dbtest_button").click(function(e) {
        e.preventDefault();
        var opts = {
            db_conn: $("#db_conn option:selected").text(),
            db_host: $("input[name=db_host]").val(),
            db_port: $("input[name=db_port]").val(),
            db_name: $("input[name=db_name]").val(),
            db_user: $("input[name=db_user]").val(),
            db_pass: $("input[name=db_pass]").val(),
        };

        console.log(opts);
        $.post("{!! route('installer.dbtest') !!}", opts, function(data) {
            $("#dbtest").html(data);
        })
    })
});
</script>
@endsection
