<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\GuestLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('auth-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

        <?php $__env->startSection('page-title'); ?>
            <?php echo e(__('Register')); ?>

        <?php $__env->stopSection(); ?>

        <?php $__env->startSection('language-bar'); ?>
                                <a href="#" class="monthly-btn btn-primary ">
                                    
                                    <select name="language" id="language" class="btn-primary btn"
                                        onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                                        <?php $__currentLoopData = App\Models\Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option class="login_lang" <?php if($lang == $language): ?> selected <?php endif; ?>
                                        value="<?php echo e(route('register', $language)); ?>"><?php echo e(ucfirst( \App\Models\Utility::getlang_fullname($language))); ?>

                                    </option>
                                    
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </a>
                            <?php $__env->stopSection(); ?>

        <?php $__env->startSection('content'); ?>
            <div class="card">
                <div class="row align-items-center text-start">
                    <div class="col-xl-6">
                        <div class="card-body">
                            <div class="">
                                <h2 class="mb-3 f-w-600"><?php echo e(__('Register')); ?></h2>
                            </div>
                            <form method="POST" action="<?php echo e(route('register')); ?>">
                                <?php if(session('statuss')): ?>
                                <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                                    <?php echo e(__('Email SMTP settings does not configured so please contact to your site admin.')); ?>

                                </div>
                            <?php endif; ?>
                                <?php echo csrf_field(); ?>
                                <div class="">

                                    <div class="form-group mb-3">
                                        <label for="fullname" class="form-label"><?php echo e(__('Full Name')); ?></label>
                                        <input type="text" class="form-control  <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="name" id="fullname" value="<?php echo e(old('name')); ?>" required
                                            autocomplete="name" autofocus placeholder="<?php echo e(__('Enter Your Name')); ?>">
                                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="workspace_name" class="form-label"><?php echo e(__('Workspace Name')); ?></label>
                                        <input type="text"
                                            class="form-control  <?php $__errorArgs = ['workspace_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="workspace" id="workspace_name" value="<?php echo e(old('workspace')); ?>" required
                                            autocomplete="workspace" placeholder="<?php echo e(__('Enter Your Workspace Name')); ?>">
                                        <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="emailaddress" class="form-label"><?php echo e(__('Email')); ?></label>
                                        <input type="email" class="form-control  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="email" id="emailaddress" value="<?php echo e(old('email')); ?>" required
                                            autocomplete="email" placeholder="<?php echo e(__('Enter Your Email')); ?>">
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label"><?php echo e(__('Password')); ?></label>
                                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="password" required autocomplete="new-password" id="password"
                                            placeholder="<?php echo e(__('Enter Your Password')); ?>">
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password_confirmation"
                                            class="form-label"><?php echo e(__('Confirm Password')); ?></label>
                                        <input type="password"
                                            class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="password_confirmation" required autocomplete="new-password"
                                            id="password_confirmation" placeholder="<?php echo e(__('Confirm Your Password')); ?>">

                                    </div>

                                    <?php if(env('RECAPTCHA_MODULE') == 'on'): ?>
                                        <div class="form-group col-lg-12 col-md-12 mt-3">
                                            <?php echo NoCaptcha::display(); ?>

                                            <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="small text-danger" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-grid">
                                        <button type="submit" id="login_button"
                                            class="btn btn-primary btn-block mt-2"><?php echo e(__('Register')); ?></button>
                                    </div>
                                    <!--  <p class="my-4 text-center">or register with</p> -->
                                    
                            </form>
                            
                            <p class="mb-2 mt-2 "><?php echo e(__('Already have an account?')); ?> <a href="<?php echo e(route('login', $lang)); ?>"
                                    class="f-w-400 text-primary"><?php echo e(__('Sign In')); ?></a></p>

                            <div class="">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 img-card-side">
                <div class="auth-img-content">
                    <img src="<?php echo e(asset('assets/images/auth/img-auth-3.svg')); ?>" alt="" class="img-fluid">
                    <h3 class="text-white mb-4 mt-5">“Attention is the new currency”</h3>
                    <p class="text-white">The more effortless the writing looks, the more effort the writer
                        actually put into the process.</p>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('custom-scripts'); ?>
        <?php if(env('RECAPTCHA_MODULE') == 'on'): ?>
            <?php echo NoCaptcha::renderJs(); ?>

        <?php endif; ?>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php /**PATH D:\laragon\www\mchd\v1\main-file\resources\views/auth/register.blade.php ENDPATH**/ ?>