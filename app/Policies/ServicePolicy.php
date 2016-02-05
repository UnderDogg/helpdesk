<?php

namespace App\Policies;

class ServicePolicy extends Policy
{
    /**
     * The service policy display name.
     *
     * @var string
     */
    protected $name = 'Service Policy';

    /**
     * The service policy actions.
     *
     * @var array
     */
    public $actions = [
        'View Services',
        'Create Service',
        'View Service',
        'Edit Service',
        'Delete Service',
    ];

    /**
     * Returns true / false if the current user can view all services.
     *
     * @return bool
     */
    public function index()
    {
        return $this->canIf('view-services');
    }

    /**
     * Returns true / false if the current user can create services.
     *
     * @return bool
     */
    public function create()
    {
        return $this->canIf('create-service');
    }

    /**
     * Returns true / false if the current user can create services.
     *
     * @return bool
     */
    public function store()
    {
        return $this->create();
    }

    /**
     * Returns true / false if the current user can create services.
     *
     * @return bool
     */
    public function show()
    {
        return $this->canIf('view-service');
    }

    /**
     * Returns true / false if the current user can edit services.
     *
     * @return bool
     */
    public function edit()
    {
        return $this->canIf('edit-service');
    }

    /**
     * Returns true / false if the current user can edit services.
     *
     * @return bool
     */
    public function update()
    {
        return $this->edit();
    }

    /**
     * Returns true / false if the current user can delete services.
     *
     * @return bool
     */
    public function destroy()
    {
        return $this->canIf('delete-service');
    }
}