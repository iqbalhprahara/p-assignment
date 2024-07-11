<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository;
use App\DataTransferObjects\UserData;
use App\Models\User;
use Illuminate\Support\Carbon;

final class EloquentUserRepository implements UserRepository
{
    /**
     * {@inheritdoc}
     */
    public function create(UserData $userData): User
    {
        return User::create([
            'uuid' => $userData->uuid,
            'gender' => $userData->gender,
            'name' => $userData->name,
            'location' => $userData->location,
            'age' => $userData->age,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(UserData $userData): User
    {
        $user = $this->getByUuid($userData->uuid);

        $user->fill([
            'gender' => $userData->gender,
            'name' => $userData->name,
            'location' => $userData->location,
            'age' => $userData->age,
        ]);

        $user->save();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $uuid): bool
    {
        $user = $this->getByUuid($uuid);

        return $user->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getByUuid(string $uuid): User
    {
        return User::findOrFail($uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function existsByUuid(string $uuid): bool
    {
        return User::where('uuid', $uuid)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function getAverageAgeByDate(Carbon $date, ?string $gender = null): int
    {
        $startOfDay = $date->clone()->startOfDay();
        $endOfDay = $date->clone()->endOfDay();

        $averageAge = User::query()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->when($gender, fn ($query) => $query->where('gender', $gender))
            ->avg('age');

        return round($averageAge);
    }
}
