<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', '<XXXXXX>');

// Project repository
set('repository', 'git@github.com:<XXXXXX>');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// number of releases
set('keep_releases', 4);

// Shared files/dirs between deploys 
set('shared_files', [
'.env',
'web/.htaccess'
]);
set('shared_dirs', [
'web/app/uploads',
'web/app/logs'
]);

// Writable dirs by web server 
set('writable_dirs', [
'web/app/uploads',
'web/app/logs'
]);
set('allow_anonymous_stats', false);

// Hosts

host('prod')
->set('hostname', '<XXXXXX>')
    ->set('remote_user', '<XXXXXX>')
    ->set('deploy_path', '~/{{application}}')
    ->set('branch', 'main');

host('test')
    ->set('hostname', '<XXXXXX>')
    ->set('remote_user', '<XXXXXXX>')
    ->set('deploy_path', '~/test-{{application}}')
    ->set('branch', 'develop')
    ->set('update_code_strategy', 'clone')
    ->set('keep_releases', 2);

// activate theme
desc('Activate Theme');
task('wp:theme', function () {
    within('{{release_path}}', function () {
        // make sure paths use current symlink
        run('echo ./vendor/bin/wp option update template_root <XXXXXX>');
        run('echo ./vendor/bin/wp option update stylesheet_root <XXXXXX>');
    });
});

// flush cache
desc('Flush Cache');
task('wp:cache_flush', function () {
    within('{{release_path}}', function () {
        // make sure paths use current symlink
        run('./vendor/bin/wp cache flush');
    });
});

// run wordpress updates
desc('Run WP updates');
task('wp:updates', function () {
    within('{{release_path}}', function () {
        // make sure paths use current symlink
        run('./vendor/bin/wp core update-db');
    });
});

// activate wordpress plugins
desc('Activate WP plugins');
task('wp:plugins', function () {
    within('{{release_path}}', function () {
        // make sure paths use current symlink
        run('./vendor/bin/wp plugin activate --all');
    });
});


desc('Deploy <XXXXXX>');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'wp:theme',
    'wp:updates',
    'wp:plugins',
    'wp:cache_flush',
    'deploy:publish',
    'deploy:success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
