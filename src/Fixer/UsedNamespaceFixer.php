<?php

namespace Certi\LegacypsrFour\Fixer;

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
        $shouldUseNamesapces  = [];

        $instantationList = $this->file->getInstantiations();

        foreach ($instantationList  as $instantation) {

            if (false == $this->registry->isGlobalScopeInstantation($instantation)) {
                $shouldUseNamesapces[] = $this->registry->getUseNamespaceByInstantation($instantation);
            }

        }

        if (count($shouldUseNamesapces)) {
            $this->fixUseNamespaces($currentUseNamespaces, $shouldUseNamesapces);
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
