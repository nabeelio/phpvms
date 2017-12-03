@extends('admin.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content">

        <div class="row">
            <div class="col-md-6">
                @component('admin.components.infobox')
                    @slot('icon', 'pe-7s-users')
                    @slot('type', 'Pilots')
                    @slot('pending', 5)
                    @slot('total', 60)
                @endcomponent
            </div>

            <div class="col-md-6">
                @component('admin.components.infobox')
                    @slot('icon', 'pe-7s-cloud-upload')
                    @slot('type', 'PIREPs')
                    @slot('pending', $pending_pireps)
                    @slot('link', route('admin.pireps.index').'?search=status:0')
                @endcomponent
            </div>

            {{--<div class="col-md-3 col-sm-6 col-xs-12">
                @component('admin.components.infobox')
                    @slot('icon', 'pe-7s-users')
                    @slot('type', 'Pilots')
                    @slot('pending', 5)
                    @slot('total', 60)
                @endcomponent
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                @component('admin.components.infobox')
                    @slot('icon', 'pe-7s-cloud-upload')
                    @slot('type', 'PIREPs')
                    @slot('pending', 5)
                    @slot('total', 60)
                @endcomponent
            </div>--}}
        </div>


        <div class="row">
            <div class="col-md-6">
                @include('admin.dashboard.announcements')
            </div>
            <div class="col-md-6">
                @include('admin.dashboard.pirep_chart')
            </div>
        </div>
    </div>
@endsection

