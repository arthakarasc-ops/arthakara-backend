<?php

namespace App\Services;

use App\Models\Collection;

class CollectionService
{
    public function createCollection(array $data)
    {
        return Collection::create($data);
    }

    public function getCollections()
    {
        return Collection::all();
    }

    public function updateCollection(int $collectionId, array $data)
    {
        $collection = Collection::find($collectionId);
        if ($collection) {
            $collection->update($data);
            return $collection;
        }
        return null;
    }

    public function deleteCollection(int $collectionId)
    {
        $collection = Collection::find($collectionId);
        if ($collection) {
            $collection->delete();
            return true;
        }
        return false;
    }
}