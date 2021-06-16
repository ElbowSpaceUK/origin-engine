<?php

namespace OriginEngine\Plugins\Dependencies\Contracts;

use OriginEngine\Plugins\Dependencies\LocalPackage;

interface LocalPackageRepository
{

    public function getById(int $id): LocalPackage;

    public function getAllThroughFeature(int $featureId): array;

}
