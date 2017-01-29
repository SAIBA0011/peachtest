@extends('layouts.app')

@section('content')
	<div class="container">
		<cards :cards="{{ auth()->user()->cards }}" :user="{{ auth()->user() }}"></cards>
	</div>
@endsection