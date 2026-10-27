<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Signatures Email | Viv'assur</title>
    <meta name="description" content="Générez et copiez facilement les signatures email professionnelles Viv'assur (versions Votre Courtier et Caspea).">
    <?= css('assets/css/style.css') ?>

    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body>

    <header class="app-header">
        <div class="app-header__content">
            <div class="app-header__brand">
                <span class="brand-badge">Viv'assur</span>
                <h1 class="brand-title">Générateur de Signatures Email</h1>
            </div>
            <div class="app-header__actions">
                <a href="<?= $site->url() ?>/panel" class="btn btn--outline" target="_blank" id="link-panel">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    Éditeur Kirby Panel
                </a>
            </div>
        </div>
    </header>

    <main class="app-main">
        <div class="app-container">
            <div class="app-intro">
                <h2>Signatures des collaborateurs (24 déclinaisons)</h2>
                <p>Chaque membre dispose de deux variantes de signature : <strong>Votre Courtier</strong> et <strong>Caspea</strong>. Les images pointent directement vers l'hébergement public <code>https://www.vivassur.be/mail-signature</code>.</p>
            </div>

            <?php 
            $signatures = $site->pages()->template('signature');
            
            if ($signatures->isNotEmpty()): 
            ?>
                <div class="signatures-grid">
                    <?php 
                    foreach ($signatures as $signature): 
                        $sigId = 'sig-' . $signature->slug(); 
                        $logoType = $signature->displayed_logo()->or('votre-courtier')->value();
                        $isCaspea = ($logoType === 'caspea');
                        $logoBadge = $isCaspea ? 'Caspea' : 'Votre Courtier';
                        $badgeClass = $isCaspea ? 'badge--caspea' : 'badge--courtier';
                    ?>
                    <article class="signature-card <?= $isCaspea ? 'signature-card--caspea' : '' ?>" id="card-<?= $signature->slug() ?>">
                        <header class="signature-card__header">
                            <div class="signature-card__info">
                                <div class="signature-card__title-row">
                                    <h2 class="signature-card__name"><?= $signature->firstname()->html() ?> <?= $signature->lastname()->html() ?></h2>
                                    <span class="signature-card__badge <?= $badgeClass ?>"><?= $logoBadge ?></span>
                                </div>
                                <span class="signature-card__role"><?= $signature->role()->html() ?></span>
                            </div>
                            <div class="signature-card__actions">
                                <button class="btn btn--primary btn--copy <?= $isCaspea ? 'btn--caspea' : '' ?>" data-id="<?= $sigId ?>" id="btn-copy-<?= $signature->slug() ?>">
                                    <svg class="icon-copy" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    <span class="btn-text">Copier cette signature</span>
                                </button>
                                <button class="btn btn--secondary btn--copy-html" data-id="<?= $sigId ?>" id="btn-copy-html-<?= $signature->slug() ?>" title="Copier le code HTML brut">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                    <span>Code HTML</span>
                                </button>
                            </div>
                        </header>
                        
                        <div class="signature-card__viewport">
                            <div class="signature-card__signature" id="<?= $sigId ?>">
                                <?= snippet('signature', ['signature' => $signature, 'site' => $site]) ?>
                            </div>
                        </div>

                        <footer class="signature-card__footer">
                            <div class="hint">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                <span>Variante avec logo <strong><?= $logoBadge ?></strong>. Cliquez sur <em>Copier cette signature</em> puis collez (Ctrl+V / Cmd+V) dans votre messagerie.</span>
                            </div>
                        </footer>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Aucune signature pour le moment</h3>
                    <p>Rendez-vous dans le Panel Kirby pour ajouter votre premier collaborateur.</p>
                    <a href="<?= $site->url() ?>/panel" class="btn btn--primary">Ouvrir le Panel</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="toast" class="toast" role="alert" aria-live="polite"></div>

    <?= js('assets/js/api/jquery.js') ?>
    <?= js('assets/js/app.js') ?>
</body>
</html>