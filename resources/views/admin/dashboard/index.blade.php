@extends('admin.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content">

        <div class="row">
            <div class="col-md-7">
                @include('admin.dashboard.announcements')
            </div>
            <div class="col-md-5">
                @component('admin.components.infobox')
                    @slot('icon', 'pe-7s-users')
                    @slot('type', 'Pilots')
                    @slot('pending', $pending_users)
                    @slot('link', route('admin.users.index').'?state='.UserState::PENDING)
                @endcomponent

                @component('admin.components.infobox')
                    @slot('icon', 'pe-7s-cloud-upload')
                    @slot('type', 'PIREPs')
                    @slot('pending', $pending_pireps)
                    @slot('link', route('admin.pireps.index').'?search=state:'.PirepState::PENDING)
                @endcomponent
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {{--@include('admin.dashboard.pirep_chart')--}}
            </div>
            <div class="col-md-6">

            </div>
        </div>


        <div class="row">
            <div class="col-md-6">

            </div>
            <div class="col-md-6">

            </div>
        </div>
    </div>
@endsection

