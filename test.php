<?php
// On teste le chemin moderne (src au lieu de lib)
$file = __DIR__ . '/vendor/doctrine/mongodb-odm/src/DocumentManagerInterface.php';

if (file_exists($file)) {
    echo "✅ TROUVÉ ! Le fichier est là : $file\n";
} else {
    echo "❌ Toujours pas... Vérifions la structure réelle de mongodb-odm :\n";
    print_r(scandir(__DIR__ . '/vendor/doctrine/mongodb-odm'));
}