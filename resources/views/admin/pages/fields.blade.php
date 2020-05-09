<div class="row">
  <div class="col-12">
    <div class="form-container">
      <h6><i class="fas fa-keyboard"></i>
        &nbsp;Page Information
      </h6>
      <div class="form-container-body">
        <div class="row">
          <div class="form-group col-sm-5">
            {{ Form::label('name', 'Page Name:') }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            <p class="text-danger">{{ $errors->first('name') }}</p>
          </div>

          <div class="form-group col-sm-5">
            {{ Form::label('icon', 'Icon:') }}
            {{ Form::text('icon', null, ['class' => 'form-control']) }}
            <p class="text-danger">{{ $errors->first('icon') }}</p>
          </div>

          <div class="form-group col-2">
            <div class="checkbox">
              <label class="checkbox-inline">
                {{ Form::label('public', 'Public:') }}
                <input name="public" type="hidden" value="0"/>
                {{ Form::checkbox('public') }}
              </label>
            </div>

            <div class="checkbox">
              <label class="checkbox-inline">
                {{ Form::label('enabled', 'Enabled:') }}
                <input name="enabled" type="hidden" value="0"/>
                {{ Form::checkbox('enabled') }}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="form-container">
      <h6><i class="fas fa-sticky-note"></i>
        &nbsp;Content
      </h6>
      <div class="form-container-body">
        <div class="row">
          <div class="form-group col-12">
            {{ Form::textarea('body', null, ['id' => 'editor', 'class' => 'editor']) }}
            <p class="text-danger">{{ $errors->first('body') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="pull-right">
      @if (!empty($page))
        {{ Form::hidden('id') }}
      @endif

      {{ Form::hidden('type',  \App\Models\Enums\PageType::HTML) }}
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
      <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Cancel</a>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    CKEDITOR.replace('editor');
  });
</script>
