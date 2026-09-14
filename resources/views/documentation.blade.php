<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentation API - Promptory</title>

    {{--
        Swagger UI est charge depuis un CDN plutot que depuis des assets locaux :
        un choix pragmatique pour ce projet (pas de pipeline npm supplementaire a
        maintenir cote backend). A reconsiderer si l'equipe travaille un jour sans
        acces Internet.
    --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.17.14/swagger-ui.min.css">

    <style>
        /*
            Cette page assume un theme clair, et un seul.

            La feuille de style de Swagger UI est ecrite en clair et n'expose
            aucune variable : sous un theme sombre, on obtient du gris fonce sur
            des blocs blancs, et la moitie de la page devient illisible. Plutot
            que de repeindre les quelques centaines de regles du paquet, la
            documentation garde une apparence unique, la sienne.
        */
        :root, .swagger-ui { color-scheme: light; }

        :root {
            --violet: #4a47c1;
            --violet-dark: #343085;
            --violet-soft: #f1f0fa;
            --bg: #fafafa;
            --surface: #ffffff;
            --border: #e4e4e8;
            --border-strong: #d4d4db;
            --text: #0a0a0f;
            --muted: #5b6270;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font: 15px/1.6 ui-sans-serif, system-ui, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .masthead { border-bottom: 1px solid var(--border); background: var(--surface); }
        .masthead .inner { max-width: 1180px; margin: 0 auto; padding: 42px 28px 28px; }

        .eyebrow {
            margin: 0;
            color: var(--violet-dark);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        h1 { margin: .5rem 0 0; font-size: 1.7rem; letter-spacing: -.02em; }

        .rule {
            display: block;
            width: 3.5rem;
            height: 3px;
            margin: .9rem 0 1rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #6e6ccd, var(--violet-dark));
        }

        .masthead p { margin: 0; max-width: 42rem; color: var(--muted); }

        #swagger-ui { max-width: 1180px; margin: 0 auto; padding: 0 28px 60px; }

        .swagger-ui .topbar { display: none; }
        .swagger-ui .info { margin: 24px 0; }
        .swagger-ui .scheme-container { background: transparent; box-shadow: none; padding: 0; }
        .swagger-ui .opblock.opblock-get { border-color: var(--border-strong); background: rgba(74,71,193,.04); }
        .swagger-ui .opblock.opblock-post { border-color: var(--border-strong); background: rgba(22,163,74,.04); }
        .swagger-ui .opblock-tag { border-color: var(--border); }
        .swagger-ui .btn.authorize { border-color: var(--violet); color: var(--violet-dark); }
        .swagger-ui .btn.authorize svg { fill: var(--violet-dark); }
    </style>
</head>
<body>
    <header class="masthead">
        <div class="inner">
            <p class="eyebrow">Documentation</p>
            <h1>API Promptory</h1>
            <span class="rule"></span>
            <p>
                Marketplace de prompts IA : catalogue, dossiers, achats, packs,
                abonnements et moderation. Jeton Bearer (Sanctum) obtenu par
                <code>POST /api/login</code>.
            </p>
        </div>
    </header>

    <div id="swagger-ui"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.17.14/swagger-ui-bundle.min.js"></script>
    <script>
        window.addEventListener('load', function () {
            window.ui = SwaggerUIBundle({
                url: '{{ route('docs.openapi') }}',
                dom_id: '#swagger-ui',
                presets: [SwaggerUIBundle.presets.apis],
                layout: 'BaseLayout',
                docExpansion: 'list',
                defaultModelsExpandDepth: -1,
                displayRequestDuration: true,
            });
        });
    </script>
</body>
</html>
