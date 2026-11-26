<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Allège les anciennes photos de produits (celles envoyées avant la réduction automatique).
 *
 *   php artisan images:reduire --dry-run     -> montre ce qui serait fait, ne change rien
 *   php artisan images:reduire               -> réduit vraiment (garde le même nom de fichier)
 *
 * SAUVEGARDEZ storage/app/public/produits avant de lancer la vraie commande.
 */
class ReduireImages extends Command
{
    protected $signature = 'images:reduire {--dry-run : Affiche seulement ce qui serait fait} {--largeur=1200} {--min-ko=300}';

    protected $description = 'Réduit les photos de produits trop lourdes (largeur max 1200 px, qualité 82)';

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error("L'extension PHP GD n'est pas installée.");
            return self::FAILURE;
        }

        ini_set('memory_limit', '512M');

        $max = (int) $this->option('largeur');
        $minOctets = (int) $this->option('min-ko') * 1024;
        $essai = (bool) $this->option('dry-run');
        $disque = Storage::disk('public');

        $avant = 0;
        $apres = 0;
        $traitees = 0;

        foreach ($disque->allFiles('produits') as $chemin) {
            $ext = strtolower(pathinfo($chemin, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $fichier = $disque->path($chemin);
            $taille = filesize($fichier);
            [$largeur, $hauteur] = @getimagesize($fichier) ?: [0, 0];

            // Déjà léger et pas trop grand : on ne touche pas
            if ($taille < $minOctets && $largeur <= $max && $hauteur <= $max) {
                continue;
            }

            $traitees++;
            $avant += $taille;

            if ($essai) {
                $this->line(sprintf('%s : %d Ko, %dx%d', $chemin, $taille / 1024, $largeur, $hauteur));
                $apres += $taille;
                continue;
            }

            $nouvelleTaille = $this->reduire($fichier, $ext, $max);
            if ($nouvelleTaille === null) {
                $this->warn("Ignorée (illisible) : {$chemin}");
                $apres += $taille;
                continue;
            }

            $apres += $nouvelleTaille;
            $this->line(sprintf('%s : %d Ko -> %d Ko', $chemin, $taille / 1024, $nouvelleTaille / 1024));
        }

        $this->info(sprintf(
            '%s%d photo(s) : %.1f Mo -> %.1f Mo',
            $essai ? '[essai] ' : '',
            $traitees,
            $avant / 1048576,
            $apres / 1048576
        ));

        return self::SUCCESS;
    }

    // Retourne la nouvelle taille en octets, ou null si l'image est illisible
    private function reduire(string $fichier, string $ext, int $max): ?int
    {
        $image = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($fichier),
            'png'         => @imagecreatefrompng($fichier),
            'webp'        => @imagecreatefromwebp($fichier),
            default       => false,
        };
        if (! $image) {
            return null;
        }

        // Les photos de téléphone portent un "sens" caché (EXIF). GD l'efface à l'enregistrement :
        // on remet la photo droite AVANT, sinon elle apparaîtrait couchée.
        if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('exif_read_data')) {
            $exif = @exif_read_data($fichier);
            $angle = match ($exif['Orientation'] ?? 1) {
                3 => 180,
                6 => -90,
                8 => 90,
                default => 0,
            };
            if ($angle !== 0) {
                $tournee = imagerotate($image, $angle, 0);
                if ($tournee) {
                    $image = $tournee;
                }
            }
        }

        $l = imagesx($image);
        $h = imagesy($image);

        if ($l > $max || $h > $max) {
            $ratio = min($max / $l, $max / $h);
            $reduite = imagescale($image, (int) round($l * $ratio), (int) round($h * $ratio));
            if ($reduite) {
                $image = $reduite;
            }
        }

        // On écrit dans un fichier temporaire, puis on remplace : jamais de photo à moitié écrite
        $temp = $fichier . '.tmp';
        $ok = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($image, $temp, 82),
            'png'         => imagepng($image, $temp, 7),
            'webp'        => imagewebp($image, $temp, 82),
        };

        if (! $ok) {
            @unlink($temp);
            return null;
        }

        // Si le résultat n'est pas plus léger (rare), on garde l'original
        if (filesize($temp) >= filesize($fichier) && $l <= $max && $h <= $max) {
            @unlink($temp);
            return filesize($fichier);
        }

        rename($temp, $fichier);
        clearstatcache(true, $fichier);

        return filesize($fichier);
    }
}
