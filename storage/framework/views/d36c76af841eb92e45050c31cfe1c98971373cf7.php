<?php $__env->startSection('page-title'); ?> <?php echo e(__('Projects')); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('links'); ?>
<?php if(\Auth::guard('client')->check()): ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('client.home')); ?>"><?php echo e(__('Home')); ?></a></li>
 <?php else: ?>
 <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
 <?php endif; ?>
<li class="breadcrumb-item"> <?php echo e(__('Projects')); ?></li>
<?php $__env->stopSection(); ?>

<?php
    $logo=\App\Models\Utility::get_file('users-avatar/');

?>
<?php $__env->startSection('action-button'); ?>
    <?php if(auth()->guard('web')->check()): ?>

     <a href="<?php echo e(route('project.export')); ?>"  class="btn btn-sm btn-primary "  data-toggle="tooltip" title="<?php echo e(__('Export Project')); ?>"
                > <i class="ti ti-file-x"></i></a>

                <a href="#"  class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-title="<?php echo e(__('Import Project')); ?>" data-url="<?php echo e(route('project.file.import' ,$currentWorkspace->slug)); ?>"  data-toggle="tooltip" title="<?php echo e(__('Import Project')); ?>"><i class="ti ti-file-import"></i> </a>

        <?php if(isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id()): ?>
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="<?php echo e(__('Create New Project')); ?>" data-url="<?php echo e(route('projects.create',$currentWorkspace->slug)); ?>" data-toggle="tooltip" title="<?php echo e(__('Add Project')); ?>">
                <i class="ti ti-plus"></i>
            </a>
          <?php endif; ?>

    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <?php if($projects && $currentWorkspace): ?>
            <div class="row mb-2">
                <div class="col-xl-12 col-lg-12 col-md-12 col-12 d-flex align-items-center justify-content-end">
                    <div class="text-sm-right status-filter">
                        <div class="btn-group mb-3">
                            <button type="button" class="btn btn-light  text-white btn_tab  bg-primary active" data-filter="*" data-status="All"><?php echo e(__('All')); ?></button>
                            <button type="button" class="btn btn-light bg-primary text-white btn_tab" data-filter=".Ongoing"><?php echo e(__('Ongoing')); ?></button>
                            <button type="button" class="btn btn-light bg-primary text-white btn_tab" data-filter=".Finished"><?php echo e(__('Finished')); ?></button>
                            <button type="button" class="btn btn-light bg-primary text-white btn_tab" data-filter=".OnHold"><?php echo e(__('OnHold')); ?></button>
                        </div>
                    </div>
                </div><!-- end col-->
            </div>

            <div class="filters-content">
                <div class="row grid">
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="col-xl-3 col-lg-4 col-sm-6 All <?php echo e($project->status); ?>">
                                <div class="card">
                                    <div class="card-header border-0 pb-0">
                                        <div class="d-flex align-items-center">
                                    <?php if($project->is_active): ?>
                                        <a href="<?php if(auth()->guard('web')->check()): ?><?php echo e(route('projects.show',[$currentWorkspace->slug,$project->id])); ?><?php elseif(auth()->guard()->check()): ?><?php echo e(route('client.projects.show',[$currentWorkspace->slug,$project->id])); ?><?php endif; ?>" class="">
                                            <img alt="<?php echo e($project->name); ?>" class="img-fluid wid-30 me-2 fix_img" avatar="<?php echo e($project->name); ?>">
                                        </a>
                                    <?php else: ?>
                                        <a href="#" class="">
                                            <img alt="<?php echo e($project->name); ?>" class="img-fluid wid-30 me-2 fix_img" avatar="<?php echo e($project->name); ?>">
                                        </a>
                                    <?php endif; ?>

                                            <h5 class="mb-0">
                                             <?php if($project->is_active): ?>
                                            <a href="<?php if(auth()->guard('web')->check()): ?><?php echo e(route('projects.show',[$currentWorkspace->slug,$project->id])); ?><?php elseif(auth()->guard()->check()): ?><?php echo e(route('client.projects.show',[$currentWorkspace->slug,$project->id])); ?><?php endif; ?>" title="<?php echo e($project->name); ?>" class=""><?php echo e($project->name); ?></a>
                                            <?php else: ?>
                                            <a href="#" title="<?php echo e(__('Locked')); ?>" class=""><?php echo e($project->name); ?></a>
                                           <?php endif; ?>
                                          </h5>
                                        </div>
                                        <div class="card-header-right">
                                            <div class="btn-group card-option">
                                                <?php if(auth()->guard('web')->check()): ?>
                                                <button type="button" class="btn dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">


                                        <?php if($project->is_active): ?>

                                                <?php if($currentWorkspace->permission == 'Owner'): ?>
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md" data-title="<?php echo e(__('Invite Users')); ?>" data-url="<?php echo e(route('projects.invite.popup',[$currentWorkspace->slug,$project->id])); ?>">
                                                        <i class="ti ti-user-plus"></i> <span><?php echo e(__('Invite Users')); ?></span>
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="lg" data-title="<?php echo e(__('Edit Project')); ?>" data-url="<?php echo e(route('projects.edit',[$currentWorkspace->slug,$project->id])); ?>">
                                                       <i class="ti ti-edit"></i> <span><?php echo e(__('Edit')); ?></span>
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md" data-title="<?php echo e(__('Share to Clients')); ?>" data-url="<?php echo e(route('projects.share.popup',[$currentWorkspace->slug,$project->id])); ?>">
                                                       <i class="ti ti-share"></i> <span><?php echo e(__('Share to Clients')); ?></span>
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                        data-size="md" data-title="<?php echo e(__('Duplicate Project')); ?>"
                                                        data-url="<?php echo e(route('project.copy', [$currentWorkspace->slug,$project->id])); ?>">
                                                        <i class="ti ti-copy"></i> <span><?php echo e(__('Duplicate')); ?></span>
                                                    </a>
                                                    <a href="#" class="dropdown-item text-danger delete-popup bs-pass-para" data-confirm="<?php echo e(__('Are You Sure?')); ?>" data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="delete-form-<?php echo e($project->id); ?>" >
                                                       <i class="ti ti-trash"></i>  <span><?php echo e(__('Delete')); ?></span>
                                                    </a>
                                                    <form id="delete-form-<?php echo e($project->id); ?>" action="<?php echo e(route('projects.destroy',[$currentWorkspace->slug,$project->id])); ?>" method="POST" style="display: none;">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                    </form>
                                                <?php else: ?>
                                                    <a href="#" class="dropdown-item text-danger bs-pass-para" data-confirm="<?php echo e(__('Are You Sure?')); ?>" data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="leave-form-<?php echo e($project->id); ?>">
                                                         <i class="ti ti-trash"></i>  <span><?php echo e(__('Delete')); ?></span>
                                                    </a>
                                                    <form id="leave-form-<?php echo e($project->id); ?>" action="<?php echo e(route('projects.leave',[$currentWorkspace->slug,$project->id])); ?>" method="POST" style="display: none;">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                    </form>
                                                <?php endif; ?>

                                        <?php else: ?>
                                            <a href="#" class="dropdown-item" title="<?php echo e(__('Locked')); ?>">
                                                <i data-feather="lock"></i> <span><?php echo e(__('Locked')); ?></span>
                                            </a>
                                        <?php endif; ?>

                                                </div>
                                                 <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2 justify-content-between">
                                            <?php if($project->status == 'Finished'): ?>
                                                <div class="col-auto"><span class="badge rounded-pill bg-success"><?php echo e(__('Finished')); ?></span>
                                            </div>
                                            <?php elseif($project->status == 'Ongoing'): ?>
                                                <div class="col-auto"><span class="badge rounded-pill bg-secondary"><?php echo e(__('Ongoing')); ?></span>
                                            </div>
                                            <?php else: ?>
                                               <div class="col-auto"><span class="badge rounded-pill bg-warning"><?php echo e(__('OnHold')); ?></span>
                                            </div>
                                            <?php endif; ?>

                                           <div class="col-auto">
                                                <p class="mb-0"><b><?php echo e(__('Due Date:')); ?></b> <?php echo e($project->end_date); ?></p>
                                            </div>
                                        </div>
                                        <p class="text-muted text-sm mt-3"><?php echo e($project->description); ?></p>
                                        <h6 class="text-muted">MEMBERS</h6>
                                        <div class="user-group mx-2">
                                              <?php $__currentLoopData = $project->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($user->pivot->is_active): ?>

                                                 <a href="#" class="img_group" data-toggle="tooltip" data-placement="top" title="<?php echo e($user->name); ?>">
                                                    <img alt="<?php echo e($user->name); ?>" <?php if($user->avatar): ?> src="<?php echo e(asset($logo.$user->avatar)); ?>" <?php else: ?> avatar="<?php echo e($user->name); ?>" <?php endif; ?>>
                                                </a>
                                             <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </div>
                                        <div class="card mb-0 mt-3">
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h6 class="mb-0"><?php echo e($project->countTask()); ?></h6>
                                                        <p class="text-muted text-sm mb-0"><?php echo e(__('Tasks')); ?></p>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <h6 class="mb-0"><?php echo e($project->countTaskComments()); ?></h6>
                                                        <p class="text-muted text-sm mb-0"><?php echo e(__('Comments')); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                          <?php if(auth()->guard('web')->check()): ?>
                            <?php if(isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id()): ?>
                             <div class="col-xl-3 col-lg-4 col-sm-6 All add_projects">
                                <a href="#" class="btn-addnew-project " style="padding: 90px 10px;" data-ajax-popup="true" data-size="md" data-title="<?php echo e(__('Create New Project')); ?>" data-url="<?php echo e(route('projects.create',$currentWorkspace->slug)); ?>">
                                <div class="bg-primary proj-add-icon">
                                <i class="ti ti-plus"></i>
                                </div>
                                <h6 class="mt-4 mb-2">Add Project</h6>
                                <p class="text-muted text-center">Click here to add New Project</p>
                                </a>
                                </div>
                            <?php endif; ?>
                           <?php endif; ?>

                </div>
            </div>
        <?php else: ?>
            <div class="container mt-5">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="page-error">
                            <div class="page-inner">
                                <h1>404</h1>
                                <div class="page-description">
                                    <?php echo e(__('Page Not Found')); ?>

                                </div>
                                <div class="page-search">
                                    <p class="text-muted mt-3"><?php echo e(__("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.")); ?></p>
                                    <div class="mt-3">
                                        <a class="btn-return-home badge-blue" href="<?php echo e(route('home')); ?>"><i class="fas fa-reply"></i> <?php echo e(__('Return Home')); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css-page'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/custom/js/isotope.pkgd.min.js')); ?>"></script>
    <script>
        $(document).ready(function () {

            $('.status-filter button').click(function () {
                $('.status-filter button').removeClass('active');
                $(this).addClass('active');

                var data = $(this).attr('data-filter');
                $grid.isotope({
                    filter: data
                })
            });

            var $grid = $(".grid").isotope({
                itemSelector: ".All",
                percentPosition: true,
                masonry: {
                    columnWidth: ".All"
                }
            })
        });
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\mchd\v1\main-file\resources\views/projects/index.blade.php ENDPATH**/ ?>