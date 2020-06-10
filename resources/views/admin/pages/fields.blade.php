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
        &nbsp;Type
      </h6>
      <div class="form-container-body">
        <div class="row">
          <div class="form-group col-12">
            {{ Form::label('type', 'Page Type') }}
            {{ Form::select('type', \App\Models\Enums\PageType::select(false), null, [
                    'id'    => 'content-type-select',
                    'class' => 'form-control select2',
                ])
            }}
            <p class="text-danger">{{ $errors->first('type') }}</p>
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
        <div id="type_content_page" class="row">
          <div class="form-group col-12">
            {{ Form::textarea('body', null, ['id' => 'editor', 'class' => 'editor']) }}
            <p class="text-danger">{{ $errors->first('body') }}</p>
          </div>
        </div>

        <div id="type_content_link" class="row">
          <div class="form-group col-12">
            {{ Form::text('link', null, ['class' => 'form-control', 'placeholder' => 'Link']) }}
            <p class="text-danger">{{ $errors->first('link') }}</p>
          </div>

          <div class="form-group col-12">
            <div class="checkbox">
              <label class="checkbox-inline">
                {{ Form::label('new_window', 'Open in new window:') }}
                <input name="new_window" type="hidden" value="0"/>
                {{ Form::checkbox('new_window') }}
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
    <div class="pull-right">
      @if (!empty($page))
        {{ Form::hidden('id') }}
      @endif

      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
      <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Cancel</a>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    CKEDITOR.replace('editor');
    const select_id = "select#content-type-select";

    function swap_content() {
      const type = parseInt($(select_id + " option:selected").val());
      console.log('content type change: ', type);

      if (type === 0) {
        $("#type_content_page").show();
        $("#type_content_link").hide();
      }
      else if (type === 1) {
        $("#type_content_page").hide();
        $("#type_content_link").show();
      }
    }

    $(select_id).change(async (e) => {
      swap_content();
    });

    swap_content();
  });
</script>
