<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Héros — Le Monde</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .monde-hero {
            text-align: center;
            padding: 4rem 2rem 2rem;
        }
        .monde-hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            text-shadow: 0 0 40px rgba(184,134,11,.4);
            margin-bottom: 1rem;
        }
        .monde-hero p {
            font-style: italic;
            color: var(--parchment-3);
            max-width: 600px;
            margin: 0 auto 2.5rem;
            font-size: 1.1rem;
            line-height: 1.9;
        }
        .monde-sep {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 3rem 0 2rem;
        }
        .monde-sep::before,
        .monde-sep::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(184,134,11,.3), transparent);
        }
        .monde-sep span {
            font-family: var(--font-rune);
            color: var(--gold-dim);
            font-size: 1.2rem;
            letter-spacing: .2em;
        }
        .section-title {
            font-family: var(--font-title);
            font-size: 1rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--gold-dim);
            margin-bottom: 1.5rem;
            padding-bottom: .5rem;
            border-bottom: 1px solid rgba(184,134,11,.15);
        }

        /* ── Lieux ── */
        .lieux-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .lieu-card {
            padding: 1.25rem 1.5rem;
            background: rgba(0,0,0,.25);
            border: 1px solid rgba(184,134,11,.15);
            border-radius: var(--radius);
            transition: border-color .2s, background .2s;
            position: relative;
            overflow: hidden;
        }
        .lieu-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold-dim), transparent);
            opacity: 0;
            transition: opacity .3s;
        }
        .lieu-card:hover { border-color: rgba(184,134,11,.4); background: rgba(184,134,11,.04); }
        .lieu-card:hover::before { opacity: 1; }
        .lieu-icon { font-size: 1.8rem; margin-bottom: .6rem; display: block; }
        .lieu-name {
            font-family: var(--font-title);
            font-size: .9rem;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: .05em;
            margin-bottom: .4rem;
        }
        .lieu-desc {
            font-size: .85rem;
            font-style: italic;
            color: rgba(242,232,208,.4);
            line-height: 1.6;
        }

        /* ── Classes ── */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }
        .classe-card {
            padding: 1.5rem;
            background: rgba(0,0,0,.25);
            border: 1px solid rgba(184,134,11,.15);
            border-radius: var(--radius);
            transition: border-color .2s, background .2s;
        }
        .classe-card:hover { border-color: rgba(184,134,11,.35); background: rgba(184,134,11,.04); }
        .classe-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: .75rem;
        }
        .classe-nom {
            font-family: var(--font-title);
            font-size: 1rem;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: .05em;
        }
        .classe-element {
            font-family: var(--font-rune);
            font-size: .72rem;
            color: var(--parchment-3);
            background: rgba(184,134,11,.1);
            border: 1px solid rgba(184,134,11,.2);
            border-radius: 20px;
            padding: .15rem .65rem;
        }
        .classe-arme {
            font-size: .85rem;
            font-style: italic;
            color: rgba(242,232,208,.4);
            margin-bottom: .6rem;
        }
        .classe-arme::before { content: '⚔ '; font-style: normal; }
        .classe-desc {
            font-size: .82rem;
            color: rgba(242,232,208,.35);
            line-height: 1.6;
        }
        .classe-variantes {
            display: flex;
            flex-wrap: wrap;
            gap: .3rem;
            margin-top: .6rem;
        }
        .variante-tag {
            font-family: var(--font-rune);
            font-size: .7rem;
            color: var(--parchment-3);
            background: rgba(184,134,11,.07);
            border: 1px solid rgba(184,134,11,.15);
            border-radius: 20px;
            padding: .1rem .55rem;
        }

        /* ── Stats ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: .75rem;
        }
        .stat-card {
            padding: 1rem 1.25rem;
            background: rgba(0,0,0,.2);
            border: 1px solid rgba(184,134,11,.1);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .stat-icon { font-size: 1.4rem; }
        .stat-label {
            font-family: var(--font-title);
            font-size: .78rem;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: .06em;
        }
        .stat-desc {
            font-size: .78rem;
            font-style: italic;
            color: rgba(242,232,208,.35);
        }

        /* ── Règles ── */
        .regles-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }
        .regles-list li {
            padding: .75rem 1.25rem .75rem 1.75rem;
            background: rgba(0,0,0,.2);
            border: 1px solid rgba(184,134,11,.1);
            border-radius: var(--radius-sm);
            font-size: .9rem;
            color: rgba(242,232,208,.55);
            font-style: italic;
            position: relative;
        }
        .regles-list li::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 2px;
            background: linear-gradient(to bottom, transparent, var(--gold-dim), transparent);
            border-radius: 2px;
        }
        .regles-list li strong {
            font-family: var(--font-title);
            font-size: .8rem;
            color: var(--gold);
            font-style: normal;
            letter-spacing: .04em;
            display: block;
            margin-bottom: .2rem;
        }

        .monde-footer {
            text-align: center;
            padding: 3rem 1rem;
        }
    </style>
</head>
<body>
    <div class="monde-hero">
        <span class="rune-symbol">✦</span>
        <h1>Le Monde des Héros</h1>
        <p>Un royaume entre lumière et ombre, où la magie ancienne côtoie l'acier des guerriers.
        Chaque chemin que vous empruntez façonne votre légende — et le destin de ce monde.</p>
        <a class="btn btn-ghost" href="index.php?page=accueil">← Retour à l'accueil</a>
    </div>

    <div class="card" style="max-width:900px">

        <!-- ── Lieux ── -->
        <div class="monde-sep"><span>✦</span></div>
        <p class="section-title">Les lieux du royaume</p>
        <div class="lieux-grid">
            <div class="lieu-card">
                <span class="lieu-icon">🛏</span>
                <div class="lieu-name">La Chambre du Réveil</div>
                <div class="lieu-desc">Là où tout commence. Un plafond de pierres grises, l'odeur de cire fondue, et l'appel inexorable de l'aventure.</div>
            </div>
            <div class="lieu-card">
                <span class="lieu-icon">🪨</span>
                <div class="lieu-name">La Pierre du Départ</div>
                <div class="lieu-desc">Un carrefour chargé d'histoire, gravé des noms de ceux qui ont choisi leur destin avant vous. Trois chemins s'y ouvrent.</div>
            </div>
            <div class="lieu-card">
                <span class="lieu-icon">🏠</span>
                <div class="lieu-name">La Maison Familiale</div>
                <div class="lieu-desc">Un refuge de souvenirs. On peut s'y entraîner seul, avec sa mère agile ou son père puissant avant de partir.</div>
            </div>
            <div class="lieu-card">
                <span class="lieu-icon">🌲</span>
                <div class="lieu-name">La Grande Forêt</div>
                <div class="lieu-desc">Une forêt ancienne et vivante. Brumes épaisses, clairières enchantées, ruines oubliées… et des dangers bien réels.</div>
            </div>
            <div class="lieu-card">
                <span class="lieu-icon">🏛</span>
                <div class="lieu-name">L'Académie</div>
                <div class="lieu-desc">Quatre tours imposantes où les destins se forgent. Mages, guerriers, assassins et supports y apprennent leur art.</div>
            </div>
            <div class="lieu-card">
                <span class="lieu-icon">⚔</span>
                <div class="lieu-name">Les Champs de Guerre</div>
                <div class="lieu-desc">La guerre a éclaté entre le Royaume et les Rebelles. Batailles, raids nocturnes, diplomatie tendue — chaque choix a un prix.</div>
            </div>
        </div>

        <!-- ── Classes ── -->
        <div class="monde-sep"><span>✦</span></div>
        <p class="section-title">Les classes de personnages</p>
        <div class="classes-grid">
            <div class="classe-card">
                <div class="classe-header">
                    <span class="classe-nom">Assassin</span>
                    <span class="classe-element">Ombre</span>
                </div>
                <div class="classe-arme">Dagues</div>
                <div class="classe-desc">Maître de la discrétion et de la mort silencieuse. Requiert une grande Agilité.</div>
                <div class="classe-variantes">
                    <span class="variante-tag">Ombre</span>
                    <span class="variante-tag">Poison</span>
                </div>
            </div>
            <div class="classe-card">
                <div class="classe-header">
                    <span class="classe-nom">Mage</span>
                    <span class="classe-element">Arcane</span>
                </div>
                <div class="classe-arme">Bâton</div>
                <div class="classe-desc">Manipulateur des forces élémentaires. Requiert des Points de Magie supérieurs à la Force.</div>
                <div class="classe-variantes">
                    <span class="variante-tag">Glace</span>
                    <span class="variante-tag">Feu</span>
                </div>
            </div>
            <div class="classe-card">
                <div class="classe-header">
                    <span class="classe-nom">Guerrier</span>
                    <span class="classe-element">Force</span>
                </div>
                <div class="classe-arme">Épée lourde / Bouclier</div>
                <div class="classe-desc">Combattant au corps à corps, pilier de toute armée. Requiert une Force supérieure aux PM.</div>
                <div class="classe-variantes">
                    <span class="variante-tag">Épée lourde</span>
                    <span class="variante-tag">Défense</span>
                </div>
            </div>
            <div class="classe-card">
                <div class="classe-header">
                    <span class="classe-nom">Épéiste Magique</span>
                    <span class="classe-element">Hybride</span>
                </div>
                <div class="classe-arme">Épée</div>
                <div class="classe-desc">Alliance rare de l'acier et de l'arcane. Requiert un équilibre parfait entre Force et PM.</div>
            </div>
            <div class="classe-card">
                <div class="classe-header">
                    <span class="classe-nom">Support</span>
                    <span class="classe-element">Vie</span>
                </div>
                <div class="classe-arme">Instrument / Sceptre</div>
                <div class="classe-desc">Gardien de ses alliés, il soigne, protège et inspire. Accessible à tous les profils.</div>
                <div class="classe-variantes">
                    <span class="variante-tag">Barde</span>
                    <span class="variante-tag">Clerc</span>
                </div>
            </div>
        </div>

        <!-- ── Caractéristiques ── -->
        <div class="monde-sep"><span>✦</span></div>
        <p class="section-title">Les caractéristiques</p>
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-icon">❤</span>
                <div><div class="stat-label">PV</div><div class="stat-desc">Points de vie</div></div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">💪</span>
                <div><div class="stat-label">Force</div><div class="stat-desc">Puissance physique</div></div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">🌀</span>
                <div><div class="stat-label">Agilité</div><div class="stat-desc">Vitesse et esquive</div></div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">✨</span>
                <div><div class="stat-label">PM</div><div class="stat-desc">Points de magie</div></div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">⚡</span>
                <div><div class="stat-label">Puissance</div><div class="stat-desc">Force magique</div></div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">🪙</span>
                <div><div class="stat-label">Argent</div><div class="stat-desc">Monnaie du royaume</div></div>
            </div>
        </div>

        <!-- ── Règles ── -->
        <div class="monde-sep"><span>✦</span></div>
        <p class="section-title">Règles de l'aventure</p>
        <ul class="regles-list">
            <li>
                <strong>Vos choix ont des conséquences</strong>
                Chaque décision modifie vos statistiques et débloque de nouveaux chemins — ou en ferme d'autres à jamais.
            </li>
            <li>
                <strong>Certains chemins sont conditionnels</strong>
                Des actions ne s'offrent à vous que si vos caractéristiques atteignent un certain seuil. Entraînez-vous avant de partir.
            </li>
            <li>
                <strong>Les succès sont permanents</strong>
                Une fois débloqués, vos succès persistent d'une partie à l'autre. Explorez tous les chemins pour les collecter.
            </li>
            <li>
                <strong>Le sommeil a un prix</strong>
                Repousser l'aventure est tentant — mais s'endormir trop souvent peut être fatal. Après vingt sommeils, tout s'arrête.
            </li>
            <li>
                <strong>Plusieurs fins vous attendent</strong>
                Victoire éclatante, mort héroïque, paix fragile ou ruines… Le monde ne se termine jamais de la même façon.
            </li>
        </ul>

    </div>

    <div class="monde-footer">
        <a class="btn" href="index.php?page=creer_personnage">Commencer l'aventure</a>
    </div>
</body>
</html>
