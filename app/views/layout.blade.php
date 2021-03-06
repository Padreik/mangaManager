<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <base href='{{ Config::get('app.url') }}' />
    
    <title>
        @if (trim($__env->yieldContent('document_title')))
            @yield('document_title')
        @else
            @yield('title')
        @endif
        - BD Manga
    </title>
    
    <!-- JQuery -->
    {{ HTML::script('lib/jquery/jquery-2.1.1.min.js') }}
    
    <!-- Bootstrap -->
    {{ HTML::style('lib/bootstrap/css/bootstrap.min.css') }}
    {{ HTML::style('lib/bootstrap/css/bootstrap-theme.min.css') }}
    {{ HTML::script('lib/bootstrap/js/bootstrap.min.js') }}
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- Custom -->
    {{ HTML::style('css/layout.css') }}
    
    @yield('header')
</head>
<body>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                <a class="navbar-brand" href="{{ URL::to('/') }}">BD Manga</a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class='dropdown'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Prêt <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                {{ HTML::linkAction('ReturnController@index', 'Retour') }}
                            </li>
                            <li>
                                {{ HTML::linkAction('LoanController@history', 'Historique') }}
                            </li>
                        </ul>
                    </li>
                    <li class='dropdown'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Importation <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                {{ HTML::linkAction('ImportController@collection', 'Collection') }}
                            </li>
                            <li>
                                {{ HTML::linkAction('ImportController@series', 'Série') }}
                            </li>
                            <li>
                                {{ HTML::linkAction('ImportController@manga', 'Manga') }}
                            </li>
                        </ul>
                    </li>
                    <li>
                        {{ HTML::linkAction('StatController@index', 'Statistiques') }}
                    </li>
                    <li class='dropdown'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Recherche <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                {{ HTML::linkAction('SeriesController@search', 'Séries') }}
                            </li>
                            <li>
                                {{ HTML::linkAction('MangaController@search', 'Mangas') }}
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <li>
                        <a href="{{ URL::action('LoanController@create') }}"><span class="glyphicon glyphicon-shopping-cart"></span><span class="badge pull-right">{{ \pgirardnet\Manga\LoanCartSessionRepository::getCount() }}</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>@yield('title')</h1>
        @yield('content')
    </div>
</body>
</html>