<?php

namespace App\Observers;

use App\Models\ProjectTemplateMilestone;

class ProjectTemplateMilestoneObserver
{

    public function saving(ProjectTemplateMilestone $projectMilestone)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $projectMilestone->last_updated_by = user()->id;

            if (company()) {
                $projectMilestone->company_id = company()->id;
            }
        }
    }

    public function creating(ProjectTemplateMilestone $projectMilestone)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $projectMilestone->added_by = user()->id;

            if (company()) {
                $projectMilestone->company_id = company()->id;
            }
        }

    }

}
