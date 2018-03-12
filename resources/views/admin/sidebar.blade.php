<div class="sidebar" data-background-color="white" data-active-color="info">

    <!--
        Tip 1: you can change the color of the sidebar's background using: data-background-color="white | black"
        Tip 2: you can change the color of the active button using the data-active-color="primary | info | success | warning | danger"
    -->


    <div class="sidebar-wrapper">
        <div class="logo" style="background: #067ec1; margin: 0px; text-align: center; min-height: 74px;">
            <a href="{{ url('/dashboard') }}">
                <img src="{{ public_asset('/assets/admin/img/logo_blue_bg.svg') }}" width="110px" style="">
            </a>
        </div>

        <ul class="nav">
            @include('admin.menu')
        </ul>

        <br />

        <div class="row">
            <div class="col-xs-12 text-center">
                <p class="small">@version</p>
            </div>
        </div>
    </div>
</div>
