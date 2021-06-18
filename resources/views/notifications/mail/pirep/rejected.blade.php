@component('mail::message')
  # PIREP Rejected!

  Your PIREP has been rejected

  @component('mail::button', ['url' => route('frontend.pireps.show', [$pirep->id])])
    View PIREP
  @endcomponent

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
