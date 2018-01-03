<!DOCTYPE html>
<html lang="de">
<head>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
    <title>PHP-Crawler</title>
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
</head>
<body>
    
    <div class="container">
        <h1><a href="{{ route('crawler.index') }}">Crawler</a></h1>
        
        <div class="row">
            <div class="col s12 m6 l6">
                
                <form action="{{ route('crawler.index') }}" method="post">
                    <div class="card">
                        <div class="card-content">
                            <p class="card-title">Wo soll der Crawl beginnen?</p>
                            <div class="input-field">
                                <input type="text" name="url" id="url" placeholder="https://google.com">
                                <label for="url">Url (Eingabe mit http bzw. https)</label>
                                <p class="small">Beispiel-URL: http://fabs:geheim@laravelt.de:9090/pfad?arg1=wert1#textanker</p>
                            </div>
                        </div>

                        <div class="card-action">
                            {{ csrf_field() }}
                            <button type="submit" class="btn waves-effect waves-light">Los geht's</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="col s12 m6 l6">
                <div class="card">
                    <div class="card-content">
                        <h2 class="card-title">Fehler und Info</h2>

                        Sites: {{ Brotzka\PhpCrawler\Models\Site::all()->count() }}<br>
                        URLs: {{ Brotzka\PhpCrawler\Models\Url::all()->count() }}<br>
                    
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li class="red-text">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col s12 m6 l6">
                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Tools</h3>
                        <p>
                            Hier können diverse Jobs manuell gestartet werden.
                        </p>

                        <a href="{{ url('horizon/dashboard') }}" target="_blank" class="btn cyan">Horizon Dashboard</a>
                    </div>
                </div>
            </div>
            
            
            <div class="col s12 m6 l6">
                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">
                            Output
                        </h3>

                        @if(isset($response))
                            <pre>
                                {{ print_r($response) }}
                            </pre>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col s12 m12 l12">
                <table class="striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Site</th>
                            <th>Anzahl URLs</th>
                            <th>Im Index seit</th>
                            <th>Zuletzt gecrawlt:</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(Brotzka\PhpCrawler\Models\Site::all() as $site)
                        <tr>
                            <td>
                                {{ $site->id }}
                            </td>
                            <td>
                                {{ $site->host }}
                            </td>
                            <td>
                                {{ count($site->urls) }}
                            </td>
                            <td>
                                {{ $site->created_at }}
                            </td>
                            <td>
                                {{ $site->updated_at }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>





<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
</body>
</html>