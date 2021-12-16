@extends("layouts.app")

@section('content')
<div id="app">
    <asteroid-page :data="'test data'"></asteroid-page>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endsection