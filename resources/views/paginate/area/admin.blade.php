@extends('Nabre::paginate.skeleton')
@section('LIST')
{!! \Nabre\Repositories\Menu\Generate::name("admin") !!}
@endsection
@section('BODY')
    @include('Nabre::paginate.skeleton.parts.main.default',['darkMode'=>true])
    @include('Nabre::paginate.skeleton.parts.body.list')
@endsection

