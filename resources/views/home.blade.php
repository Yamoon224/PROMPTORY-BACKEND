<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Promptory</title>
    <style>
        :root {
            --violet: #4a47c1;
            --violet-dark: #343085;
            --ink: #0a0a0f;
            --muted: #6b6b76;
            --line: #e4e4e8;
            --surface: #ffffff;
            --canvas: #fafafa;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --ink: #f2f2f5;
                --muted: #a1a1ac;
                --line: #34343d;
                --surface: #202127;
                --canvas: #17181c;
            }
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 3rem 1.25rem;
            background: var(--canvas);
            color: var(--ink);
            font: 15px/1.6 ui-sans-serif, system-ui, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .shell { max-width: 46rem; margin: 0 auto; }

        .badge {
            display: inline-block;
            padding: .25rem .6rem;
            border-radius: 2px;
            background: linear-gradient(135deg, #6e6ccd 0%, var(--violet) 48%, var(--violet-dark) 100%);
            color: #fff;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        h1 { margin: 1rem 0 .25rem; font-size: 1.6rem; letter-spacing: -.02em; }

        .rule {
            display: block;
            width: 3.5rem;
            height: 3px;
            margin: .75rem 0 1.25rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #6e6ccd, var(--violet-dark));
        }

        p { color: var(--muted); margin: 0 0 1rem; }

        .card {
            margin-top: 2rem;
            border: 1px solid var(--line);
            border-radius: 2px;
            background: var(--surface);
            overflow: hidden;
        }

        .card > div { padding: 1.1rem 1.25rem; }
        .card > div + div { border-top: 1px solid var(--line); }
        .card .accent { padding: 0; height: 3px; background: linear-gradient(135deg, #6e6ccd, var(--violet-dark)); }

        h2 { margin: 0 0 .4rem; font-size: .95rem; }

        a { color: var(--violet-dark); font-weight: 500; }

        code {
            padding: .1rem .35rem;
            border-radius: 2px;
            background: rgba(74, 71, 193, .12);
            font-family: ui-monospace, "Cascadia Code", Menlo, monospace;
            font-size: .85em;
        }

        footer { margin-top: 2.5rem; color: var(--muted); font-size: .85rem; }
    </style>
</head>
<body>
    <main class="shell">
        <span class="badge">API REST</span>
        <h1>Promptory</h1>
        <span class="rule"></span>
        <p>
            Backend de la marketplace de prompts IA : catalogue, dossiers personnels,
            achats, packs, abonnements premium et moderation du contenu.
        </p>

        <div class="card">
            <div class="accent"></div>
            <div>
                <h2>Documentation de l'API</h2>
                <p style="margin:0">
                    Specification OpenAPI et bac a sable : <a href="{{ route('docs') }}">/docs</a>
                    &middot; format brut : <a href="{{ route('docs.openapi') }}">/docs/openapi.json</a>
                </p>
            </div>
            <div>
                <h2>Sonde de sante</h2>
                <p style="margin:0">
                    <code>GET /api/health</code> - verifie la connectivite a la base de donnees.
                </p>
            </div>
            <div>
                <h2>Interfaces</h2>
                <p style="margin:0">
                    La marketplace et le tableau de bord sont servis par l'application
                    Next.js du dossier <code>web/</code>.
                </p>
            </div>
        </div>

        <footer>
            Environnement : <code>{{ app()->environment() }}</code> &middot;
            Laravel {{ app()->version() }}
        </footer>
    </main>
</body>
</html>
