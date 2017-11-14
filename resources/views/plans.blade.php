@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Canada Media</h1>
                <h2>Tours</h2>
                <p class="lead">
                    Please select from the following plans for a tour of your selected city!
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Montreal</h3>
                    </div>
                    <div class="panel-body">
                        <h4 class="text-center">
                            $90 / month
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Toronto</h3>
                    </div>
                    <div class="panel-body">
                        <h4 class="text-center">
                            $100 / month
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <form method="POST" action="{{ url('/') }}">
                    <h2 class="text-muted">Destination</h2>
                    <select class="form-control" name="plan">
                        <option value="montreal">Montreal</option>
                        <option value="toronto">Toronto</option>
                    </select>
                    <br>
                    <input type="submit" class="btn btn-primary btn-lg btn-block" value="Subscribe">
                </form>

            </div>
        </div>
    </div>
@endsection
