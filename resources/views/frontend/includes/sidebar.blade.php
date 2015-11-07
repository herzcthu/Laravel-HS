          <!-- Left side column. contains the logo and sidebar -->
          <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

              <!-- Sidebar user panel (optional) -->
              @if(access()->user())
              <div class="user-panel">
                <div class="pull-left image">
                  <img src="{!! (!empty(access()->user()->avatar)? access()->user()->avatar: asset('img/backend/user2-160x160.png')) !!}" class="img-circle profile-avatar" alt="User Image" />
                </div>
                <div class="pull-left info">
                  
                  <p>{{ access()->user()->name }}</p>
                  
                  <!-- Status -->
                  <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
              </div>
              
              @endif
              
              <!-- search form (Optional) -->
              <!--form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                  <input type="text" name="q" class="form-control" placeholder="Search..."/>
                  <span class="input-group-btn">
                    <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </form>
              <!-- /.search form -->

              <!-- Sidebar Menu -->
              <ul class="sidebar-menu">
                <!-- Optionally, you can add icons to the links -->
                @if(!access()->user()->can('view_backend'))
                <li class="{{ Active::pattern('dashboard') }}"><a href="{!!route('frontend.dashboard')!!}"><span>{!! _t('Dashboard') !!}</span></a></li>
                @endif
                @permission('add_result')
                <li class="{{ Active::pattern('data/projects/*') }}"><a href="{!!url('data/projects')!!}"><span>{!! _t('Projects') !!}</span></a></li>
                @endpermission
                @permission('manage_participant')
                <li class="{{ Active::pattern('admin/participants/*') }}"><a href="{!! route('admin.participants.index')!!}"><span>{!! _t('Participants') !!}</span></a></li>
                @endpermission                
                @permission('add_result')
                <li class="{{ Active::pattern('admin/language/*') }}"><a href="{!! route('admin.language.index')!!}"><span>{!! _t('Translation') !!}</span></a></li>
                @endpermission
              </ul><!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
          </aside>
