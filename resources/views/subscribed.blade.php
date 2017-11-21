@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="text-center">Thank You</h1>
                <h2 class="text-center">Thank you for your subscription.</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center">
                <p class="lead">You have chosen the <span class="text-bold">{{ $plan->getName() }}</span> plan.</p>
                <p>{{ $plan->getRecurringAmount() }} {{ $plan->getCurrency() }} per {{ $plan->getRecurringPeriodUnit() }}</p>
            </div>
        </div>
    </div>
@endsection
