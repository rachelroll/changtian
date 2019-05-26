<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'changtian-api');

// Project repository
set('repository', 'git@github.com:rachelroll/changtian.git');

set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader --ignore-platform-reqs
');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('writable_mode', 'chown');

set('keep_releases', 2);

// Shared files/dirs between deploys 
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server 
set('writable_dirs', []);

task('test', function () {
    writeln('Hello world');
});

// Hosts

host('oeaudio.com')
    ->user('root')
    ->set('deploy_path', '/var/www/{{application}}');


// Tasks

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
