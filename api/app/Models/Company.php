<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\NodeTrait;

/**
 * App\Models\Company
 *
 * @property int                             $id
 * @property int                             $_lft
 * @property int                             $_rgt
 * @property int|null                        $parent_id
 * @property string                          $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\CompanyFactory            factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 *
 * @property-read \Kalnoy\Nestedset\Collection<int, Company> $children
 * @property-read int|null $children_count
 * @property-read Company|null $parent
 *
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    ancestorsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    ancestorsOf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    applyNestedSetScope(?string $table = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    countErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    d()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    defaultOrder(string $dir = 'asc')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    descendantsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    fixSubtree($root)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    fixTree($root = null)
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    getNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    getPlainNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    getTotalErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    hasChildren()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    hasParent()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    isBroken()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    leaves(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    makeGap(int $cut, int $height)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    moveNode($key, $position)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    orWhereDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    orWhereNodeBetween($values)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    orWhereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    rebuildSubtree($root, array $data, $delete = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    rebuildTree(array $data, $delete = false, $root = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    reversed()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    root(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereAncestorOrSelf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereIsAfter($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereIsBefore($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereIsLeaf()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereIsRoot()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    whereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    withDepth(string $as = 'depth')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Company    withoutRoot()
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Station> $stations
 * @property-read int|null $stations_count
 *
 * @mixin \Eloquent
 */
class Company extends VirtaModel
{
    use NodeTrait;

    public const CACHE_KEY_COMPANIES_FLAT_MAP = 'companies_flat_map';
    public const CACHE_KEY_INDIVIDUAL         = 'company';

    protected $fillable = [
        'name',
        'parent_id',
    ];

    protected static function booted(): void
    {
        $postCreateUpdateDel = fn () => Cache::delete(self::CACHE_KEY_COMPANIES_FLAT_MAP);

        static::created($postCreateUpdateDel);
        static::updated($postCreateUpdateDel);
        static::deleted($postCreateUpdateDel);
    }

    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }

    public function allStationsCollection(): Collection
    {
        /**
         * A better approach would be with a single direct query. Something like:
         *
         * SELECT s.id AS station_id, c.name AS company_name, s.company_id
         * FROM stations s JOIN companies c ON c.id = s.company_id
         * WHERE c._lft BETWEEN (:parent_company_lft + 1) AND :parent_company_rgt;
         *
         * @phpstan-ignore-next-line
         */
        $companyIds   = $this->descendants()->pluck('id');
        $companyIds[] = $this->id;
        return Station::whereIn('company_id', $companyIds)->get();
    }

    public function parentCompanies()
    {
        return $this->ancestors()->get()->makeHidden(['_lft', '_rgt']);
    }

    public function subsidiaries()
    {
        return $this->descendants()->get()->makeHidden(['_lft', '_rgt']);
    }
}
