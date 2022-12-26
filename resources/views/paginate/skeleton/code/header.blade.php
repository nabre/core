<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>window.Laravel={ csrfToken: "{{ csrf_token() }}" }</script>
<title>{{ Breadcrumbs::pageTitle() }}</title>

<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript" async src="{{ mix('js/app.js')  }}"></script>
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
@livewireStyles
