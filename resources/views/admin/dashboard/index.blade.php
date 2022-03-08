@extends('admin.app')
@section('title', 'Dashboard')
@section('content')
  <div class="content">
    @if($cron_problem_exists)
      <div class="alert alert-danger" role="alert">
        The cron has not run in more than 12 hours; make sure it's setup and check logs at
        <span class="text-monospace bg-gradient-dark">storage/logs/cron.log</span>.
        <a href="{{ docs_link('cron') }}" target="_blank">See the docs</a>
      </div>
    @endif

    <div class="row">
      <div class="col-md-7">
        @include('admin.dashboard.news')
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
        {{-- @include('admin.dashboard.pirep_chart') --}}
      </div>
      <div class="col-md-6">
        {{-- Intentionally Left Blank --}}
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        {{-- Intentionally Left Blank --}}
      </div>
      <div class="col-md-6">
        {{-- Intentionally Left Blank --}}
      </div>
    </div>
  </div>
@endsection
@section('scripts')
  @parent
  <script>
    $(document).ready(function () {
      $(document).on('submit', 'form.pjax_news_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#pjax_news_wrapper', {push: false});
      });

      /*$(document).on('pjax:complete', function () {
          $(".select2").select2();
      });*/
    });
  </script>
@endsection
