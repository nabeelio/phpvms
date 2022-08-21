<div class="row">
  <!-- Code Field -->
  <div class="form-group col-sm-12">
    <div class="form-container">
      <h6>
        <i class="fas fa-users mr-2"></i>
        @if($users_count > 0) {{ $users_count.' users are assigned to this role' }} @else No Users @endif
      </h6>
      <div class="form-container-body">
        @if($users_count > 0)
          <div class="row">
            <div class="col-sm-12">
              @foreach($users as $u)
                &nbsp;&bull;&nbsp;<a href="{{ route('admin.users.edit', [$u->id]) }}">{{ $u->ident.' '.$u->name }}</a>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
