<?php

namespace Wannanbigpig\LaravelScoutElastic\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class SearchableModel extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id'];

    public function getIdAttribute(): int
    {
        return 1;
    }

    public function searchableAs(): string
    {
        return 'table';
    }

    public function scoutMetadata(): array
    {
        return [];
    }

    public function toSearchableArray(): array
    {
        return ['id' => 1];
    }
}
