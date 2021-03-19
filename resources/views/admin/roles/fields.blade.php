<div class="row">
  <!-- Code Field -->
  <div class="form-group col-sm-4">
    <div class="form-container">
      <h6><i class="fas fa-keyboard"></i>
        &nbsp;Name
      </h6>
      <div class="form-container-body">
        <div class="row">
          <div class="form-group col-sm-12">
            {{ Form::text('display_name', null, ['class' => 'form-control']) }}
            <p class="text-danger">{{ $errors->first('display_name') }}</p>
          </div>
        </div>
      </div>
    </div>
    <div class="form-container">
      <h6><i class="fas fa-check-square"></i>
        Features
      </h6>
      <div class="form-container-body">
        <div class="row">
          <div class="form-group col-sm-12">
            <div class="checkbox">
              {{ Form::hidden('disable_activity_checks', 0) }}
              {{ Form::checkbox('disable_activity_checks', 1) }}
              {{ Form::label('disable_activity_checks', 'disable activity checks') }}
              <p class="text-danger">{{ $errors->first('disable_activity_checks') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Permissions Field -->
  <div class="form-group col-sm-8">
    <div class="form-container">
      <h6><i class="fas fa-check-square"></i>
        &nbsp;Permissions
      </h6>
      <div class="form-container-body">
        <div class="row">
          <div class="form-group col-sm-12">
            @foreach($permissions as $p)
              <div class="checkbox">
                <label class="checkbox-inline">
                  {{ Form::hidden('permissions[]', false) }}
                  {{ Form::checkbox('permissions[]', $p->id) }}
                  {{ Form::label('permissions[]', $p->display_name) }} - <span
                    class="description">{{$p->description}}</span>
                </label>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <!-- Submit Field -->
  <div class="form-group col-sm-12">
    <div class="pull-right">
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
      <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Cancel</a>
    </div>
  </div>
</div>
