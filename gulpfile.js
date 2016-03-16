var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix
        // Copy webfont files from /vendor directories to /public directory.
        .copy('vendor/fortawesome/font-awesome/fonts', 'public/fonts')
        .copy('vendor/twbs/bootstrap-sass/assets/fonts/bootstrap', 'public/fonts')
        .copy('vendor/twbs/bootstrap/dist/js/bootstrap.min.js', 'public/js/vendor')
        .copy('resources/assets/vendor/jquery/dist/jquery.min.js', 'public/js/vendor/jquery-2.1.4.min.js')
        .copy('resources/assets/vendor/jquery-ui/jquery-ui.min.js', 'public/js/vendor/jquery-ui')
        .copy('resources/assets/vendor/jquery-ui/themes', 'public/css/vendor/jquery-ui/themes')
        .copy('resources/assets/vendor/bootstrap-filestyle/src', 'public/js/vendor/bootstrap-filestyle')
        .copy('vendor/blueimp/jquery-file-upload/js', 'public/js/vendor/blueimp')
        .copy('resources/assets/vendor/DataTables-1.10.9/js/jquery.dataTables.min.js', 'public/js/vendor/datatables/jquery.dataTables.min.js')
        .copy('resources/assets/vendor/DataTables-1.10.9/js/dataTables.bootstrap.min.js', 'public/js/vendor/datatables/dataTables.bootstrap.min.js')
        .copy('resources/assets/vendor/canvasjs-1.7.0/jquery.canvasjs.min.js', 'public/js/vendor/canvasjs/jquery.canvasjs.min.js')

        .sass([ // Process front-end stylesheets
                'frontend/main.scss'
            ], 'resources/assets/css/frontend/main.css')
        .styles([  // Combine pre-processed CSS files
                'frontend/main.css'
            ], 'public/css/frontend.css')
        .scripts([ // Combine front-end scripts
                'plugins.js',
                'frontend/main.js'
            ], 'public/js/frontend.js')

        .sass([ // Process back-end stylesheets
            'backend/main.scss',
            'backend/skin.scss'
        ], 'resources/assets/css/backend/main.css')
        .styles([ // Combine pre-processed CSS files
                'bootstrap.css',
                'font-awesome.css',
                //'jquery.dataTables.css',
                'backend/main.css',
                'backend/skin.css',
                'backend/image-picker.css'
            ], 'public/css/backend.css')
        .scripts([ // Combine back-end scripts
                'plugins.js',
                'backend/main.js'
            ], 'public/js/backend.js')

        // Apply version control
        .version(["public/css/frontend.css", "public/js/frontend.js", "public/css/backend.css", "public/js/backend.js"]);
});

/**
 * Uncomment for LESS version
 */
/*elixir(function(mix) {
    mix
        // Copy webfont files from /vendor directories to /public directory.
        .copy('vendor/fortawesome/font-awesome/fonts', 'public/fonts')
        .copy('vendor/twbs/bootstrap/fonts', 'public/fonts')
        .copy('vendor/twbs/bootstrap/dist/js/bootstrap.min.js', 'public/js/vendor')

        .less([ // Process front-end stylesheets
            'frontend/main.less'
        ], 'resources/assets/css/frontend/main.less')
        .styles([  // Combine pre-processed CSS files
            'frontend/main.css'
        ], 'public/css/frontend.css')
        .scripts([ // Combine front-end scripts
            'plugins.js',
            'frontend/main.js'
        ], 'public/js/frontend.js')

        .less([ // Process back-end stylesheets
            'backend/AdminLTE.less',
        ], 'resources/assets/css/backend/AdminLTE.less')
        .styles([ // Combine pre-processed CSS files
            'bootstrap.css',
            'font-awesome.css',
            'backend/main.css',
            'backend/skin.css'
        ], 'public/css/backend.css')
        .scripts([ // Combine back-end scripts
            'plugins.js',
            'backend/main.js'
        ], 'public/js/backend.js')

        // Apply version control
        .version(["public/css/frontend.css", "public/js/frontend.js", "public/css/backend.css", "public/js/backend.js"]);
});*/