<div class="sidebar" data-background-color="black" data-active-color="info">

    <!--
        Tip 1: you can change the color of the sidebar's background using: data-background-color="white | black"
        Tip 2: you can change the color of the active button using the data-active-color="primary | info | success | warning | danger"
    -->


    <div class="sidebar-wrapper">
        <div class="logo" style="background: #067ec1; margin: 0px; text-align: center; min-height: 74px;">
            <img src="/assets/frontend/img/logo_blue_bg.svg" width="110px" style="">
        </div>

        <ul class="nav">
            @include('admin.menu')
        </ul>
    </div>
</div>
