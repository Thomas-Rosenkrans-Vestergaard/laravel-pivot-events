<?php

namespace Signifly\PivotEvents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasPivotEvents
{
    protected $pivotChanges = [];

    public function setPivotChanges(string $type, string $relation, array $ids = []): void
    {
        collect($ids)->each(function ($attributes, $id) use ($type, $relation) {
            data_set($this->pivotChanges, "{$type}.{$relation}.{$id}", $attributes);
        });
    }

    public function getPivotChanges($type = null): Collection
    {
        return $type
            ? collect(data_get($this->pivotChanges, $type))
            : collect($this->pivotChanges);
    }

    public function getPivotChangeIds($type, $relation): Collection
    {
        return collect($this->getPivotChanges("{$type}.{$relation}"))->keys();
    }

    public function resetPivotChanges(): void
    {
        $this->pivotChanges = [];
    }

    public static function pivotAttaching($callback)
    {
        static::registerModelEvent('pivotAttaching', $callback);
    }

    public static function pivotAttached($callback)
    {
        static::registerModelEvent('pivotAttached', $callback);
    }

    public static function pivotDetaching($callback)
    {
        static::registerModelEvent('pivotDetaching', $callback);
    }

    public static function pivotDetached($callback)
    {
        static::registerModelEvent('pivotDetached', $callback);
    }

    public static function pivotUpdating($callback)
    {
        static::registerModelEvent('pivotUpdating', $callback);
    }

    public static function pivotUpdated($callback)
    {
        static::registerModelEvent('pivotUpdated', $callback);
    }

    public function firePivotAttachingEvent($halt = true)
    {
        return $this->fireModelEvent('pivotAttaching', $halt);
    }

    public function firePivotAttachedEvent($halt = false)
    {
        return $this->fireModelEvent('pivotAttached', $halt);
    }

    public function firePivotDetachingEvent($halt = true)
    {
        return $this->fireModelEvent('pivotDetaching', $halt);
    }

    public function firePivotDetachedEvent($halt = false)
    {
        return $this->fireModelEvent('pivotDetached', $halt);
    }

    public function firePivotUpdatingEvent($halt = true)
    {
        return $this->fireModelEvent('pivotUpdating', $halt);
    }

    public function firePivotUpdatedEvent($halt = false)
    {
        return $this->fireModelEvent('pivotUpdated', $halt);
    }

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_merge(
            parent::getObservableEvents(),
            [
                'pivotAttaching', 'pivotAttached',
                'pivotDetaching', 'pivotDetached',
                'pivotUpdating', 'pivotUpdated',
            ]
        );
    }

    /**
     * Instantiate a new BelongsToMany relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relationName
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    protected function newBelongsToMany(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null
    ) {
        return new BelongsToMany($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }
}
