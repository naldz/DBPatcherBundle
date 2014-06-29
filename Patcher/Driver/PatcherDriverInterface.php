<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher\Driver;

interface PatcherDriverInterface
{
    /**
     * Applies an sql patch
     *
     * @param string $patchPath
     * @return null
     */
    public function applyPatch($sqlPatchPath);

    /**
     * Get a PDO connection
     *
     * @return PDO
     */
    public function getConnection();
}