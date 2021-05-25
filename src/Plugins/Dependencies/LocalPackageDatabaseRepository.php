<?php

namespace OriginEngine\Plugins\Dependencies;

use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;

class LocalPackageDatabaseRepository implements LocalPackageRepository
{

    public function getById(int $id): LocalPackage
    {
        return LocalPackage::findOrFail($id);
    }

    public function getAllThroughFeature(int $featureId): array
    {
        return LocalPackage::where('parent_feature_id', $featureId)->get()->toArray();
    }
}