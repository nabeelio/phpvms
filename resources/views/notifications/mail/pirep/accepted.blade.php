@component('mail::message')
  # PIREP Accepted!

  Your PIREP has been accepted

  @component('mail::button', ['url' => route('frontend.pireps.show', [$pirep->id])])
    View PIREP
  @endcomponent

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
