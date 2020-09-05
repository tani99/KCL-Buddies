<!doctype html>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>{{ config('app.name', 'Questionnaire') }}</title>
   <!-- Custom CSS -->
   <link href='https://fonts.googleapis.com/css?family=Ropa+Sans' rel='stylesheet' type='text/css'>
   <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" >
   <link href="{{ asset('css/questionnaire.css') }}" rel="stylesheet" type="text/css" >

</head>
<body>

  @yield('title')
  <br>
  @yield('questions')

<script src="{{asset('js/questionnaire.js')}}"></script>


</body>

</html>
