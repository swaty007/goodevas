<?php

namespace Brackets\CraftablePro\Translations\Scanners\Internal;

use Brackets\CraftablePro\Translations\Repositories\LanguageLineRepository;
use Brackets\CraftablePro\Translations\Scanners\Contracts\ScannerInterface;
use Illuminate\Support\Facades\File;

abstract class BaseScanner implements ScannerInterface
{
    private array $scannedPaths = [];
    private array $scanPatterns = [];

    public function __construct()
    {
        $this->setUpScanPatterns();
    }

    abstract public function setUpScanPatterns();

    public function addScannedPaths(array $scannedPaths): self
    {
        $this->scannedPaths = $scannedPaths;

        return $this;
    }

    public function scanAndSaveTranslations(): void
    {
        collect($this->scannedPaths)->each(function ($path) {
            if (! File::exists($path)) {
                return true;
            }

            collect(File::allFiles($path))->each(function ($file) {
                collect($this->scanPatterns)->each(function ($options, $pattern) use ($file) {
                    if (preg_match_all("/$pattern/si", $file->getContents(), $matches)) {
                        $groups = $options['groupPosition'] ? $matches[$options['groupPosition']] : null;
                        collect($matches[$options['keyPosition']])->each(function ($keyItem, $index) use ($groups) {
                            if (! $keyItem) {
                                $group = "*";
                                $key = $groups[$index];
                            } else {
                                $key = $keyItem;
                                $group = $groups ? $groups[$index] : '*';
                            }
                            app(LanguageLineRepository::class)->createLanguageLineIfDoesntExist($group, $key);
                        });
                    }
                });
            });
        });
    }

    /**
     * @param $pattern
     * @param $keyPosition
     * @param $groupPosition
     * @return $this
     */
    public function addScanPattern($pattern, $keyPosition = 4, $groupPosition = 2)
    {
        $this->scanPatterns[$pattern] = [
            'groupPosition' => $groupPosition,
            'keyPosition' => $keyPosition,
        ];

        return $this;
    }
}
