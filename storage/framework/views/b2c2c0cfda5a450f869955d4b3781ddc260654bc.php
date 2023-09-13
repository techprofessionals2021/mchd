<?php
 $logo=\App\Models\Utility::get_file('logo/');
if(Auth::user()->type == 'admin')
{
$setting = App\Models\Utility::getAdminPaymentSettings();
    if ($setting['color']) {
        $color = $setting['color'];
    }
    else{
    $color = 'theme-3';
    }
    $dark_mode = $setting['cust_darklayout'];
    $cust_theme_bg =$setting['cust_theme_bg'];
    $SITE_RTL = env('SITE_RTL');
     $company_logo = App\Models\Utility::get_logo();
}
else { 
    $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
    $color = $setting->theme_color;
    $dark_mode = $setting->cust_darklayout; 
    $SITE_RTL = $setting->site_rtl;
    $cust_theme_bg = $setting->cust_theme_bg;
    $company_logo = App\Models\Utility::getcompanylogo($currentWorkspace->id);

       if($company_logo == '' || $company_logo == null){
              $company_logo = App\Models\Utility::get_logo();
                     
           }
}

   if($color == '' || $color == null){
      $settings = App\Models\Utility::getAdminPaymentSettings();
      $color = $settings['color'];           
   }

   if($dark_mode == '' || $dark_mode == null){
     $company_logo = App\Models\Utility::get_logo();
      $dark_mode = $settings['cust_darklayout'];
   }

   if($cust_theme_bg == '' || $dark_mode == null){
      $cust_theme_bg = $settings['cust_theme_bg'];
   }

    if($SITE_RTL == '' || $SITE_RTL == null){
      $SITE_RTL = env('SITE_RTL');
   }
?>
<nav class="dash-sidebar light-sidebar <?php echo e((isset($cust_theme_bg) && $cust_theme_bg == 'on') ? 'transprent-bg':''); ?>">
    <div class="navbar-wrapper">
      <div class="m-header main-logo">
        <a href="<?php echo e(route('home')); ?>" class="b-brand">
          <!-- ========   change your logo hear   ============ -->

           <img
            src="<?php echo e($logo.$company_logo.'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')); ?>" alt="logo" class="sidebar_logo_size" />
        </a>
      </div>
      <div class="navbar-content">
        <ul class="dash-navbar">
           <?php if(\Auth::guard('client')->check()): ?>
              <li class="dash-item dash-hasmenu">
                <a href="<?php echo e(route('client.home')); ?>" class="dash-link <?php echo e((Request::route()->getName() == 'home' || Request::route()->getName() == NULL || Request::route()->getName() == 'client.home') ? ' active' : ''); ?>">
                  <span class="dash-micon"><i class="ti ti-home"></i></span>
                  <span class="dash-mtext"><?php echo e(__('Dashboard')); ?></span>


                </a>
              </li>
           <?php else: ?>
             <li class="dash-item dash-hasmenu">
                <a href="<?php echo e(route('home')); ?>" class="dash-link  <?php echo e((Request::route()->getName() == 'home' || Request::route()->getName() == NULL || Request::route()->getName() == 'client.home') ? ' active' : ''); ?>">
                 <?php if(Auth::user()->type == 'admin'): ?> <span class="dash-micon"><i class="ti ti-user"></i></span>
                  <span class="dash-mtext"><?php echo e(__('Users')); ?></span><?php else: ?><span class="dash-micon"><i class="ti ti-home"></i></span>
                  <span class="dash-mtext"><?php echo e(__('Dashboard')); ?></span> <?php endif; ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if(isset($currentWorkspace) && $currentWorkspace): ?>
            <?php if(auth()->guard('web')->check()): ?>
          <li class="dash-item dash-hasmenu">
            <a href="<?php echo e(route('users.index',$currentWorkspace->slug)); ?>" class="dash-link <?php echo e((Request::route()->getName() == 'users.index') ? ' active' : ''); ?>"><span class="dash-micon"> <i data-feather="user"></i></span><span
                    class="dash-mtext"><?php echo e(__('Users')); ?></span></a>
          </li>

            


         




          
        <?php elseif(auth()->guard()->check()): ?>
            
           <?php endif; ?>
                <?php endif; ?>
         
         <?php if(Auth::user()->type == 'admin'): ?>
          


             

         <?php endif; ?>
         

         <?php if(Auth::user()->type == 'admin'): ?>  
         <?php endif; ?>
         
      </div>
    </div>
  </nav>
<?php /**PATH D:\laragon\www\mchd\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>