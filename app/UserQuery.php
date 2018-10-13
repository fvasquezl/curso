<?php
/**
 * Created by PhpStorm.
 * User: fvasquez
 * Date: 12/10/18
 * Time: 02:49 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Builder;


class UserQuery extends Builder
{
    use FiltersQueries;

    public function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    /**
     * @return array
     */
    protected function filterRules(): array
    {
        $rules = [
            'search' => 'filled',
            'state' => 'in:active,inactive',
            'role' => 'in:admin,user',
        ];
        return $rules;
    }

    public function filterBySearch($search)
    {
        return $this->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
    }
    public function filterByState($state)
    {
        return $this->where('active', $state == 'active');
    }


}