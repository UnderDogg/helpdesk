<?php

namespace App\Jobs;

use App\Models\Issue;

class OpenIssue extends Job
{
    /**
     * @var Issue
     */
    protected $issue;

    /**
     * Constructor.
     *
     * @param Issue $issue
     */
    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * Closes an issue.
     */
    public function handle()
    {
        $this->issue->closed = false;
        $this->issue->closed_at = null;

        $this->issue->save();
    }
}
