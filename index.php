<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.6/css/pico.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.6/css/pico.colors.min.css" />
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
</head>

<body>
    <header class="container">
        <nav>
            <ul>
            <li>
                
                <h1><img src="Logo.png" alt="" style="height:64px">Handyverkauf</h1>
                </li>
            </ul>
            <ul>
                
                <li>
                    <input type="search" name="search" placeholder="search..." hx-get="/search/" hx-trigger="load, input changed, search" hx-target="#search-results" hx-indicator="loading"> 
                </li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Results: <?php echo $_GET["search"] ?? "" ?></h1>

        <section id="search-results"></div>
        
        <div aria-busy="true" id="loading" class="htmx-indicator"></div>
    </main>
</body>

</html>