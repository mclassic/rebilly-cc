@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Checkout Challeeeeenge!</h1>
        <form method="POST" action="{{ url('/') }}">
            <div class="row">
                <div class="col-xs-12 form-group">
                    <h2 class="text-muted">Destination</h2>
                    <select class="form-control" name="plan">
                        <option value="montreal">Montreal</option>
                        <option value="toronto">Toronto</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <h2 class="text-muted">Personal Information</h2>
                    <label>
                        First Name<br>
                        <input type="text" name="first_name" class="form-control">
                    </label>
                    <br>
                    <label>
                        Last Name<br>
                        <input type="text" name="last_name" class="form-control">
                    </label>
                    <br>
                    <label>
                        EMail<br>
                        <input type="text" name="email" class="form-control">
                    </label>
                    <br>
                    <label>
                        Phone<br>
                        <input type="text" name="phone" class="form-control">
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <h2 class="text-muted">Billing Address</h2>
                    <label>
                        Address<br>
                        <input type="text" name="address1" class="form-control">
                    </label>
                    <br>
                    <label>
                        Address 2<br>
                        <input type="text" name="address2" class="form-control">
                    </label>
                    <br>
                    <label>
                        City<br>
                        <input type="text" name="city" class="form-control">
                    </label>
                    <br>
                    <label>
                        Province<br>
                        <input type="text" name="province" class="form-control">
                    </label>
                    <br>
                    <label>
                        Postal Code<br>
                        <input type="text" name="postalcode" class="form-control">
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <h2 class="text-muted">Billing Information</h2>
                    <label>
                        Card Number<br>
                        <input type="text" name="ccnum" class="form-control">
                    </label>
                    <br>
                    <label>
                        Exp Month<br>
                        <input type="text" name="ccmonth" class="form-control">
                    </label>
                    <br>
                    <label>
                        CVV<br>
                        <input type="text" name="cvv" class="form-control">
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <input type="submit" value="Subscribe" class="btn btn-lg btn-primary">
                </div>
            </div>
        </form>
    </div>
@endsection
