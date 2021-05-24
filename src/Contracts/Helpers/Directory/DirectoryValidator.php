<?php

namespace OriginEngine\Contracts\Helpers\Directory;

/**
 * Check if the given directory is valid
 */
interface DirectoryValidator
{

    /**
     * Is the given directory valid?
     *
     * @param string $directory As referenced from the project directory set by the user.
     * @return bool
     */
    public function isValid(string $directory): bool;

}
