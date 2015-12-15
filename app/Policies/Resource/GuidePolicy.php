<?php

namespace App\Policies\Resource;

use App\Policies\Policy;

class GuidePolicy extends Policy
{
    /**
     * {@inheritdoc}
     */
    public $actions = [
        'View Unpublished',
        'View Guide',
        'Create Guide',
        'Edit Guide',
        'Delete Guide',
    ];

    /**
     * Allows only users with specific permission
     * to view guides that are unpublished.
     *
     * @return bool
     */
    public function viewUnpublished()
    {
        return $this->can('view-unpublished');
    }

    /**
     * Allows only users with specific permission to create guides.
     *
     * @return bool
     */
    public function create()
    {
        return $this->can('create-guide');
    }

    /**
     * Allows only users with specific permission to create guides.
     *
     * @return bool
     */
    public function store()
    {
        return $this->create();
    }

    /**
     * Allows only users with specific permission to edit guides.
     *
     * @return bool
     */
    public function edit()
    {
        return $this->can('edit-guide');
    }

    /**
     * Allows only users with specific permission to edit guides.
     *
     * @return bool
     */
    public function update()
    {
        return $this->edit();
    }

    /**
     * Allows only users with specific permission to delete guides.
     *
     * @return bool
     */
    public function destroy()
    {
        return $this->can('delete-guide');
    }
}
