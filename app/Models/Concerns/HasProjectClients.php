<?php

namespace App\Models\Concerns;

use App\Support\SystemManager;
use Illuminate\Support\Collection;

trait HasProjectClients
{
    abstract protected function projectOwnerColumn(): string;

    public function scopeForClient($query, int $userId)
    {
        $column = $this->projectOwnerColumn();

        return $query->where(function ($q) use ($userId, $column) {
            $q->where($column, $userId)
                ->orWhereHas('clients', fn ($c) => $c->where('users.id', $userId));
        });
    }

    public function isClientMember(int $userId): bool
    {
        $column = $this->projectOwnerColumn();

        if ((int) $this->{$column} === $userId) {
            return true;
        }

        return $this->clients()->where('users.id', $userId)->exists();
    }

    public function attachProjectClient(int $userId): void
    {
        $this->clients()->syncWithoutDetaching([$userId]);
    }

    /** كل عملاء المشروع (المالك + المضافون عبر pivot) */
    public function allProjectClients()
    {
        $ownerId = (int) $this->{$this->projectOwnerColumn()};
        $clients = $this->relationLoaded('clients')
            ? $this->clients
            : $this->clients()->get();

        if ($ownerId && !$clients->contains('id', $ownerId)) {
            $owner = \App\Models\User::find($ownerId);
            if ($owner) {
                $clients = $clients->push($owner);
            }
        }

        return $clients->unique('id')->values();
    }

    public function userCanViewAllProjectIssues(int $userId, ?string $role = null): bool
    {
        if ($role === 'admin') {
            return true;
        }

        return $this->isClientMember($userId);
    }

    /** شركاء + أدمن (طارق بن كلبان) + مدير المشروع — للمهام والاجتماعات وغيرها */
    public function assignableTeamMembers(): Collection
    {
        return SystemManager::assignableUsers($this);
    }
}
