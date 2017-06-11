@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Dashboard</h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">pireps</span>
                            <span class="info-box-number">41,410</span>
                            {{--<div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>--}}
                            <span class="progress-description">
                              20 to approve
                            </span>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </div>
            </div>
        </div>
    </div>
@endsection

