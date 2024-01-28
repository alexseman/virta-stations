<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\VirtaModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|VirtaModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VirtaModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VirtaModel query()
 * @method static \Database\Factories\VirtaModelFactory            factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class VirtaModel extends Model
{
    use HasFactory;

    public const PAGINATION_PER_PAGE  = 10;
    public const CACHE_KEY_INDIVIDUAL = 'virta_model';

    public static function getIndividualCacheKey(int $id): string
    {
        return static::CACHE_KEY_INDIVIDUAL . ':' . $id;
    }

    public static function getOrSetForever(int $id)
    {
        if (Cache::has(static::getIndividualCacheKey($id))) {
            return Cache::get(static::getIndividualCacheKey($id));
        }

        $cacheables = static::find($id)->getAttributes();

        Cache::put(
            static::getIndividualCacheKey($id),
            $cacheables
        );

        return $cacheables;
    }
}
