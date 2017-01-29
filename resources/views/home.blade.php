@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <ul class="list-group">
                        @foreach($products as $product)
                            <li class="list-group-item">
                                {{ $product->name }}
                                <span class="pull-right">
                                    <form method="POST" action="/purchases">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" href="/purcase" class="btn btn-sm btn-primary">Purchase {{ money_format('R %2n', $product->price / 100) }}</button>
                                    </form>                                    
                                </span>
                            </li>
                        @endforeach
                    </ul>                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
