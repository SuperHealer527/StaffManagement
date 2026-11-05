<?php

namespace App\Console\Commands;

use App\Services\AssignmentWorkflowService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('staff-management:synchronize-statuses')]
#[Description('過去イベントとアサインの状態を要件に従って自動更新します')]
class SynchronizeStaffManagementStatuses extends Command
{
    public function handle(AssignmentWorkflowService $assignmentWorkflowService): int
    {
        $counts = $assignmentWorkflowService->synchronizePastEvents();
        $this->info("イベント {$counts['events']}件、アサイン {$counts['assignments']}件を更新しました。");

        return self::SUCCESS;
    }
}
