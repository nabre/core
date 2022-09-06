@extends('Nabre::paginate.skeleton')
@section('LIST')
{!! \Nabre\Repositories\Menu\Generate::name("user") !!}
@endsection
@section('BODY')
    @include('Nabre::paginate.skeleton.parts.main.default')
    @include('Nabre::paginate.skeleton.parts.body.list')
@endsection

