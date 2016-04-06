</html>
<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}" />
        <title>@yield('title', app_name())</title>
        <meta name="description" content="@yield('meta_description', 'Default Description')">
        <meta name="author" content="@yield('author', 'Anthony Rappa')">
        @yield('meta')

        @yield('before-styles-end')
        {!! HTML::style(elixir('css/backend.css')) !!}
        {!! HTML::style(elixir('css/frontend.css')) !!}
        @yield('after-styles-end')

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        @include('includes.partials.jsvar')
        
    </head>
    <body class="skin-blue sidebar-collapse">
        <div class="wrapper">
          @include('frontend.includes.header')
          @include('frontend.includes.sidebar')

          <!-- Content Wrapper. Contains page content -->
          <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
              @yield('page-header')
              <ol class="breadcrumb">
                @yield('breadcrumbs')
              </ol>
            </section>

            <!-- Main content -->
            <section class="content">
              @include('includes.partials.messages')
              @yield('content')
            </section><!-- /.content -->
          </div><!-- /.content-wrapper -->

          @include('frontend.includes.footer')
        </div><!-- ./wrapper -->

        <!--script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script-->
        <script>window.jQuery || document.write('<script src="{{asset('js/vendor/jquery-2.1.4.min.js')}}"><\/script>')</script>        
        {!! HTML::script('js/vendor/jquery-ui/jquery-ui.min.js') !!}
        <script type="text/javascript">
            $.widget.bridge('uibutton', $.ui.button);
            $.widget.bridge('uitooltip', $.ui.tooltip);        
        </script>
        {!! HTML::script('js/vendor/bootstrap.min.js') !!}
        {!! HTML::script('js/vendor/blueimp/vendor/jquery.ui.widget.js') !!}
        {!! HTML::script('js/vendor/blueimp/jquery.iframe-transport.js') !!}
        {!! HTML::script('js/vendor/blueimp/jquery.fileupload.js') !!}
        {!! HTML::script('js/vendor/image-picker/image-picker.min.js') !!}
        {!! HTML::script('js/vendor/dynamicForm.js') !!}
        {!! HTML::script('js/vendor/datatables/datatables.min.js') !!}
        {!! HTML::script('js/vendor/datatables/dataTables.bootstrap.min.js') !!}

        @yield('before-scripts-end')
        {!! HTML::script(elixir('js/backend.js')) !!}
        {!! HTML::script(elixir('js/frontend.js')) !!}
        @yield('after-scripts-end')
        @stack('scripts')
    </body>
</html>
