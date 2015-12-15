<?php

namespace App\Policies;

use App\Models\User;
use Orchestra\Authorization\Policy as AuthorizationPolicy;
use Orchestra\Model\Role;

abstract class Policy extends AuthorizationPolicy
{
    /**
     * The policy actions.
     *
     * @var array
     */
    public $actions = [];

    /**
     * The authorization name.
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set the ACL name to the current classes name.
        $this->name = static::class;
    }

    /**
     * Intercepts all checks and allows administrators
     * to perform all tasks.
     *
     * @param User $user
     *
     * @return bool
     */
    public function before(User $user)
    {
        if ($user->is($this->admin()->name)) {
            return true;
        }
    }

    /**
     * Returns the administrators role.
     *
     * @return null|Role
     */
    protected function admin()
    {
        return Role::admin();
    }
}
