@component('mail::message')
  # New PIREP Submitted

  A new PIREP has been submitted by {{ $pirep->user->ident }} {{ $pirep->user->name }}

  @component('mail::button', ['url' => route('admin.pireps.edit', [$pirep->id])])
    View PIREP
  @endcomponent

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
