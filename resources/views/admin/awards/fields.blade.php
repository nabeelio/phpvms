<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success">
            Awards that can be granted to pilots.
        </div>
        <br />
    </div>
</div>
<div class="row">
<div class="form-group col-sm-6">
    {!! Form::label('title', 'Title:') !!}&nbsp;<span class="required">*</span>
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        This will be the title of the award
    </div>
    {!! Form::text('title', null, ['class' => 'form-control']) !!}
</div>



<div class="form-group col-sm-6">
    {!! Form::label('image', 'Image:') !!}
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        This is the image of the award. Be creative!
    </div>
    {!! Form::text('image', null, ['class' => 'form-control', 'placeholder' => 'Enter the url of the image location']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}&nbsp
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        This is the description of the award.
    </div>
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <div class="pull-right">
        {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
        <a href="{!! route('admin.awards.index') !!}" class="btn btn-warn">Cancel</a>
    </div>
</div>
</div>
