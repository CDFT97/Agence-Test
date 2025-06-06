<!DOCTYPE html>
<html>

@include('partials.head')

<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark ">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{route('agence')}}" style="2em;">Agence</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Agencia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Proyectos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Administrativo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Comercial</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Financiero</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Usuario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>
    </div>

    @yield('scripts')
    @include('partials.scripts')
</body>

</html>
