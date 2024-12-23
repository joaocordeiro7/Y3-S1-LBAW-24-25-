<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'The Bulletin') }}</title>

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
    </head>
    <body>
        <main>
            <header>
                <h1><a href="{{ url('/home') }}">The Bulletin</a></h1>
                @if (Auth::check())
                <a class="button" href="{{ url('/createPosts') }}"> Post News </a> <a class="button" href="{{ url('/logout') }}"> Logout </a> <a href="{{url('/users/'.Auth::user()->user_id)}}">{{ html_entity_decode(Auth::user()->username, ENT_QUOTES, 'UTF-8') }}</a>
                @else
                <a class="button" href="{{ url('/login') }}"> Login </a> <a class="button" href="{{ url('/register') }}"> Register </a>
                @endif
                @if (Auth::check() && Auth::user()->isAdmin())
                <a class="button" href="{{route('adminDashboard')}}">Dashboard</a>
                @endif
                @if (Auth::check())
                    <span class="dropdown"><i class="fa-solid fa-bell"></i></span>
                    @php
                        $notfs = \App\Http\Controllers\UserController::userNotf();
                    @endphp
                    @include('partials.notf',['notfs'=>$notfs])
                @endif
            </header>
            <section id="content">
                @yield('content')
            </section>
            <div class="container">
                <hr>
                <footer>
                    <div class="row">
                        <div class="col-lg-12">
                            <p>Copyright &copy; The Bulletin 2024</p>
                            <a class="button" href="{{ route('features') }}">Features</a>
                            <a class="button" href="{{ route('contacts') }}">Contacts</a>
                            <a class="button" href="{{ route('aboutUs') }}">About Us</a> 
                        </div>
                    </div>
                </footer>
            </div>
        </main>
        @yield('scripts')
        <script type="text/javascript" src="{{ url('js/app.js') }}"  defer>
        </script>
    </body>
</html>