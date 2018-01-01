<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('name', 'Name:') !!}&nbsp;&nbsp;<span class="required">*</span>
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Required Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('required', 'Required:') !!}
        {!! Form::hidden('required', 0) !!}
        {!! Form::checkbox('required', null) !!}
    </div>
</div>
<div class="row">
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.pirepfields.index') !!}" class="btn btn-default">Cancel</a>
    </div>
</div>
