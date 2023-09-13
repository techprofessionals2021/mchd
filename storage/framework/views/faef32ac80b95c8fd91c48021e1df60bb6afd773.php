<div class="card-body table-border-style">
    <div class="table-responsive ">
        <table class="table mb-0">
            <thead>
            <tr>
                <th class="text-muted"><?php echo e(__('Task')); ?></th>
                <?php $__currentLoopData = $days['datePeriod']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $perioddate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th class="text-dark m-0 "><?php echo e($perioddate->format('D d M')); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <th class="th-sm">
                    <span class="pr-1"><?php echo e(__('Total')); ?></span>
                </th>
            </tr>
            </thead>
            <tbody>
    
            <?php if(isset($allProjects) && $allProjects == true): ?>
    
                <?php $__currentLoopData = $timesheetArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $timesheet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    
                    <tr class="">
                        <td colspan="9"><span data-tooltip="Project" class="project-name pad_row custom-tooltip"><?php echo e($timesheet['project_name']); ?></span></td>
                    </tr>
    
                    <?php $__currentLoopData = $timesheet['taskArray']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $taskTimesheet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    
                        <tr>
                            <td colspan="9">
                                <div data-tooltip="Task" class="task-name  ml-3 pad_row custom-tooltip">
                                    <?php echo e($taskTimesheet['task_name']); ?>

                                </div>
                            </td>
                        </tr>
                    
                        <?php $__currentLoopData = $taskTimesheet['dateArray']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateTimeArray): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    
                            <tr>
                                <td>
                                    <div data-tooltip="User" class="task blue ml-5 custom-tooltip">
                                        <?php echo e($dateTimeArray['user_name']); ?>

                                    </div>
                                </td>
    
                                <?php $__currentLoopData = $dateTimeArray['week']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateSubArray): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    
                                <td>
                                    <?php if(auth()->guard('client')->check()): ?>
                                        <div  class="form-control border-dark wid-120"><?php echo e($dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00'); ?></div>
                                    <?php elseif(auth()->guard()->check()): ?>
                                        <?php if((Auth::user()->currant_workspace == $currentWorkspace->id) && ($currentWorkspace->created_by == Auth::user()->id ) || (Auth::user()->id == $dateTimeArray['user_id'])): ?>
                                            <div role="button" class="form-control border-dark wid-120" title="<?php echo e($dateSubArray['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet')); ?>" data-ajax-timesheet-popup="true" data-type="<?php echo e($dateSubArray['type']); ?>" data-user-id="<?php echo e($dateTimeArray['user_id']); ?>" data-project-id="<?php echo e($timesheet['project_id']); ?>" data-task-id="<?php echo e($taskTimesheet['task_id']); ?>" data-date="<?php echo e($dateSubArray['date']); ?>"
                                            data-url="<?php echo e($dateSubArray['url']); ?>"><?php echo e($dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00'); ?></div>
                                        <?php else: ?>
                                            <div  class="form-control border-dark wid-120"><?php echo e($dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00'); ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
    
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <div class="total form-control bg-transparent border-dark wid-120">
                                        <?php echo e($dateTimeArray['totaltime']); ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
            <?php else: ?>
                <?php $__currentLoopData = $timesheetArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $timesheet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="task-name ml-3">
                                <?php echo e($timesheet['task_name']); ?>

                            </div>
                        </td>
    
                        <?php $__currentLoopData = $timesheet['dateArray']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $datetime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td>
                                <?php if(auth()->guard('client')->check()): ?>
                                    <div  class="form-control border-dark"><?php echo e($datetime['time'] != '00:00' ? $datetime['time'] : '00:00'); ?></div>
                                <?php elseif(auth()->guard()->check()): ?>
                                    <?php if((Auth::user()->currant_workspace == $currentWorkspace->id) && ($currentWorkspace->created_by == Auth::user()->id )): ?>
                                        <div role="button" class="form-control border-dark wid-120" title="<?php echo e($datetime['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet')); ?>" data-ajax-timesheet-popup="true" data-type="<?php echo e($datetime['type']); ?>" data-task-id="<?php echo e($timesheet['task_id']); ?>" data-date="<?php echo e($datetime['date']); ?>" data-url="<?php echo e($datetime['url']); ?>"><?php echo e(isset($datetime['total_task_time']) ? $datetime['total_task_time'] : '00:00'); ?></div>
                                    <?php else: ?>
                                        <div role="button" class="form-control border-dark wid-120" title="<?php echo e($datetime['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet')); ?>" data-ajax-timesheet-popup="true" data-type="<?php echo e($datetime['type']); ?>" data-task-id="<?php echo e($timesheet['task_id']); ?>" data-date="<?php echo e($datetime['date']); ?>" data-url="<?php echo e($datetime['url']); ?>"><?php echo e($datetime['time'] != '00:00' ? $datetime['time'] : '00:00'); ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <div class="total form-control bg-transparent border-dark wid-120">
                                <?php echo e($timesheet['totaltime']); ?>

                            </div>
                        </td>
                    </tr>
    
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
    
            </tbody>
            <tfoot>
            <tr class="footer-total bg-primary">
                <td><?php echo e(__('Total')); ?></td>
    
                <?php $__currentLoopData = $totalDateTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $totaldatetime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td>
                        <div class=" form-control border-dark wid-120" >
                            <?php echo e($totaldatetime != '00:00' ? $totaldatetime : '00:00'); ?>

                        </div>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td>
                    <div class="form-control border-dark wid-120" >
                        <?php echo e($calculatedtotaltaskdatetime); ?>

                    </div>
                </td>
               
            </tr>
            
            </tfoot>
            
    
        </table>
    </div>
    </div>
    <div class="text-center d-flex align-items-center justify-content-center  mt-4 mb-5">
        <h5 class="f-w-900 me-2 mb-0"><?php echo e(__('Time Logged:')); ?></h5>
        <span class="p-2  f-w-900 rounded  bg-primary d-inline-block border border-dark"> <?php echo e($calculatedtotaltaskdatetime); ?></span>
    </div>
    
    <style type="text/css">
         .table thead {
        line-height: 30px !important;
     }
    
    </style>
<?php /**PATH D:\laragon\www\mchd\v1\main-file\resources\views/projects/timesheet-week.blade.php ENDPATH**/ ?>