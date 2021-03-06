          <!-- Main Header -->
          <header class="main-header">

            <!-- Logo -->
            <a href="{!!route('home')!!}" class="logo">{!! _t('<b>EMS</b> Myanmar') !!}</a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
              <!-- Sidebar toggle button-->
              <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
              </a>
              
              <!-- Navbar Right Menu -->
              <div class="navbar-custom-menu">
               
                <ul class="nav navbar-nav">
                    <li>
                    <select id="lang" name="lang" class="form-control">
                    {{--  @foreach(config('translation.locales') as $langcode => $langname)
                      <li><a href="{{ $langcode }}">{!! $langname !!}</a></li>
                      @endforeach 
                      --}}
                      <option value="{!! url('lang/en/') !!}" @if(Translation::getRoutePrefix() == 'en') {{ 'selected' }} @endif>English</option>
                      <option value="{!! url('lang/my/') !!}" @if(Translation::getRoutePrefix() == 'my') {{ 'selected' }} @endif>{{ _t('Myanmar') }}</option>
                    </select>
                    @push('scripts')
                        <script>
                            $(function(){
                              // bind change event to select
                              $('#lang').on('change', function () {
                                  var url = $(this).val(); // get selected value
                                  if (url) { // require a URL
                                      window.location = url; // redirect
                                  }
                                  return false;
                              });
                            });
                        </script>
                    @endpush
                   </li>
                  <!-- User Account Menu -->
                  <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <!-- The user image in the navbar-->
                      <img src="{!! (!empty(access()->user()->avatar)? access()->user()->avatar: asset('img/backend/user2-160x160.png')) !!}" class="user-image profile-avatar" alt="User Image"/>
                      <!-- hidden-xs hides the username on small devices so only the image appears. -->
                      <span class="hidden-xs">{{ access()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                      <!-- The user image in the menu -->
                      <li class="user-header">
                        <img src="{!! (!empty(access()->user()->avatar)? access()->user()->avatar: asset('img/backend/user2-160x160.png')) !!}" class="img-circle profile-avatar" alt="User Image" />
                        <p>
                          {{ access()->user()->name }} - {{ access()->user()->role->name }}
                          <small>{!! _t('Member since :time', array('time' => date('j-M-Y', strtotime(access()->user()->created_at)))) !!}</small>
                        </p>
                      </li>
                      <!-- Menu Body -->
                      <!--li class="user-body">
                        <div class="col-xs-4 text-center">
                          <a href="#">Link</a>
                        </div>
                        <div class="col-xs-4 text-center">
                          <a href="#">Link</a>
                        </div>
                        <div class="col-xs-4 text-center">
                          <a href="#">Link</a>
                        </div>
                      </li-->
                      <!-- Menu Footer-->
                      <li class="user-footer">
                        <div class="pull-left">
                            <a href="{!! url('profile') !!}" class="btn btn-default btn-flat">{!! _t('Profile') !!}</a>
                        </div>
                        <div class="pull-right">
                          <a href="{!!url('auth/logout')!!}" class="btn btn-default btn-flat">{!! _t('Sign out') !!}</a>
                        </div>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
            </nav>
          </header>
