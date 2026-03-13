<?php
// echo "<h3>🕵️ Recherche du fichier par le serveur web...</h3>";

// $baseDir = dirname(__DIR__) . '/vendor/doctrine/mongodb-odm';

// // Test 1 : Le serveur a-t-il le droit de lire le dossier racine ?
// if (!is_dir($baseDir)) {
//     die("❌ ERREUR DE PERMISSIONS : Le serveur web (PHP-FPM) n'a même pas le droit de voir le dossier $baseDir. C'est le fameux mur de Linux !");
// }

// echo "✅ Le serveur peut lire le dossier vendor.<br>";

// // Test 2 : On fouille tous les sous-dossiers
// $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
// $foundPath = null;

// foreach ($iterator as $file) {
//     if ($file->getFilename() === 'DocumentManagerInterface.php') {
//         $foundPath = $file->getPathname();
//         break;
//     }
// }

// if ($foundPath) {
//     echo "✅ <b>FICHIER TROUVÉ !</b> Voici son chemin exact :<br>";
//     echo "<code style='color:blue;'>" . $foundPath . "</code><br><br>";
    
//     // Test 3 : L'interface est-elle reconnue si on la charge avec ce vrai chemin ?
//     require_once $foundPath;
//     echo "Interface chargée en mémoire ? " . (interface_exists('Doctrine\ODM\MongoDB\DocumentManagerInterface') ? "<b>OUI</b> 🎉" : "<b>NON</b> 😭");
// } else {
//     echo "❌ <b>FICHIER INTROUVABLE</b> dans tout le dossier mongodb-odm. Il manque vraiment !";
// }
// die();

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
