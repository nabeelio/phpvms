@extends('app')
@section('title', __('home.welcome.title'))

@section('content')
<div class="bg-cover bg-center h-screen" style="background-image: url({{ public_asset('assets/sasva/media/landing_bg.jpg') }});">
  <div class="h-screen flex justify-center items-center" style="background-color: rgba(75, 85, 99, .8) !important">
    <div class="container mx-auto px-4 pt-24">
      <div class="relative isolate lg:px-8">
        <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
          <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-4xl">SAS Virtual Airlines</h1>
            <p class="mt-6 text-lg leading-8 text-gray-100">SASva was created to provide simulator pilots with an environment to operate real world routes and schedules. To do this, we support pilots with a vast array of tools and utilities to improve their experience.</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
              <a href="#" class="rounded-md bg-blue-800 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">Start flying</a>
              <a href="#" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-black shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">Our story</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
