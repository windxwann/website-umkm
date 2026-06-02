<?php
$regex = '/[\x{1F300}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1FA70}-\x{1FAFF}\x{1F004}-\x{1F0CF}\x{1F170}-\x{1F251}\x{2B50}\x{231A}\x{231B}\x{23E9}-\x{23EC}\x{23F0}\x{23F3}\x{25FD}\x{25FE}\x{2614}\x{2615}\x{2648}-\x{2653}\x{267F}\x{2693}\x{26A1}\x{26AA}\x{26AB}\x{26BD}\x{26BE}\x{26C4}\x{26C5}\x{26CE}\x{26D4}\x{26EA}\x{26F2}\x{26F3}\x{26F5}\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}\x{2935}\x{2B05}-\x{2B07}\x{2B1B}\x{2B1C}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{FE0F}]/u';

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$modifiedFiles = [];
foreach ($dir as $file) {
    if ($file->isFile() && preg_match('/\.(php|blade\.php|js|css)$/', $file->getFilename())) {
        $path = $file->getPathname();
        if (str_contains($path, 'vendor') || str_contains($path, 'node_modules') || str_contains($path, 'storage') || str_contains($path, '.git')) {
            continue;
        }
        $content = file_get_contents($path);
        if (preg_match($regex, $content)) {
            $newContent = preg_replace($regex, '', $content);
            file_put_contents($path, $newContent);
            $modifiedFiles[] = $path;
        }
    }
}
echo "Removed emojis from " . count($modifiedFiles) . " files:\n";
echo implode("\n", $modifiedFiles);
