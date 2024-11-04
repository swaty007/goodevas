<?php

namespace Brackets\CraftablePro\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait PublishableTrait
{
    private function hasPublishedTo(): bool
    {
        return in_array('published_to', $this->dates, true);
    }

    /**
     * Scope a query to only include published models.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('published_at', '<=', Carbon::now())
            ->whereNotNull('published_at')
            ->when($this->hasPublishedTo(), static function ($query) {
                return $query->where(static function ($query2) {
                    $query2->where('published_to', '>=', Carbon::now())
                        ->orWhereNull('published_to');
                });
            });
    }

    /**
     * Scope a query to only include unpublished models.
     */
    public function scopeUnpublished(Builder $query): Builder
    {
        return $query->where('published_at', '>', Carbon::now())->orWhereNull('published_at')
            ->when($this->hasPublishedTo(), static function ($query) {
                $query->orWhere('published_to', '<', Carbon::now());
            });
    }

    public function isPublished(): bool
    {
        if ($this->published_at === null) {
            return false;
        }

        return $this->published_at->lte(Carbon::now()) && ($this->hasPublishedTo() ? ($this->published_to->gte(Carbon::now()) || $this->published_to === null) : true);
    }

    public function isUnpublished(): bool
    {
        return ! $this->isPublished();
    }

    public function publish(): bool
    {
        $data = ['published_at' => Carbon::now()->toDateTimeString()];

        if ($this->hasPublishedTo() && $this->published_to->lte(Carbon::now())) {
            $data['published_to'] = null;
        }

        return $this->update($data);
    }

    public function unpublish(): bool
    {
        return $this->update([
            'published_at' => null,
        ]);
    }
}
