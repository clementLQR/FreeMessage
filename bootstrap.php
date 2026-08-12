<?php
    // on charge les paquets installés avec Composer
    require_once 'vendor/autoload.php';
    // on charge les templates (gabarit de la vue)
    // un objet de class FilesystemLoader est créé et stocké dans la variable $loader;
    $loader = new \Twig\Loader\FilesystemLoader('./template');
    // on initialise l'environnement Twig
    // en créant un objet de classe Environment qui sera stocké dans la variable $twig
    $twig = new \Twig\Environment($loader);

    // jeton CSRF unique par session, exposé à tous les templates
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $twig->addGlobal('csrf_token', $_SESSION['csrf_token']);

    // filtre pour afficher les dates au format "28 juillet"
    $twig->addFilter(new \Twig\TwigFilter('date_fr', function ($date) {
        if (empty($date)) {
            return '';
        }
        $mois = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin',
                 7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];
        $d = new DateTime($date);
        return (int) $d->format('j') . ' ' . $mois[(int) $d->format('n')];
    }));
    ////////////////////////
    // on aurait pu utiliser des options si besoin
    // $options = array(
    // debug => false,
    // cache => true,
    // );
    //$twig = new \Twig\Environment($loader, $options);
?>