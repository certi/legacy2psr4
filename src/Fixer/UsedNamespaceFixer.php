<?php

namespace Certi\LegacypsrFour\Fixer;

use Certi\LegacypsrFour\Item\Instantation;
use Certi\LegacypsrFour\PhpFile;
use Certi\LegacypsrFour\PhpFileRegistry;

class UsedNamespaceFixer extends AbstractFixer
{

    /**
     * Adds or replaces the namespace.
     */
    public function run()
    {
        // Check instantations
        $currentUseNamespaces = $this->file->getUsesNamespaces();
        $shouldUseNamespaces  = [];

        $instantationList = $this->file->getInstantiations();

        /**
         * @var Instantation $instantation
         */
        foreach ($instantationList  as $instantation) {
            if (false == $this->registry->isGlobalScopeInstantation($instantation)) {
                $shouldUseNamespaces[] = $this->registry->getUseNamespaceByInstantation($instantation);
            }
        }

        if (count($shouldUseNamespaces)) {
            $this->fixUseNamespaces($currentUseNamespaces, $shouldUseNamespaces);
        }

    }

    /**
     * Checks "uses" replaces or/and adds the needed
     *
     * @param $currentUseNamespaces
     * @param $shouldUseNamesapces
     */
    protected function fixUseNamespaces($currentUseNamespaces, $shouldUseNamesapces)
    {

    }

}
