<?php

namespace Deployer;

require 'recipe/symfony.php';

// Project name
set('application', 'gold-pillar');

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

set('default_stage', 'dev');

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('104.248.216.49')
    ->user('deploy')
    ->stage('dev')
    ->identityFile('~/.ssh/id_rsa')
    ->roles('app')
    ->set('deploy_path', '/var/www/gold-pillar');

host('178.238.133.7.srvlist.ukfast.net')
    ->user('deploy')
    ->port(2020)
    ->stage('prod')
    ->identityFile('~/.ssh/id_rsa')
    ->roles('app')
    ->set('deploy_path', '/var/www/gold-pillar');

// Tasks
/*
 * Overriding the default task that makes git repository clone
 * to copy the files directly. Since the code has already been downloaded and
 * tested by the pipeline.
 */
task('deploy:update_code', function () {
    runLocally('tar -cf app.tar .');
    upload('app.tar', '{{release_path}}/app.tar');
    cd('{{release_path}}');
    run('tar -xf app.tar');
    run('rm app.tar');
    runLocally('rm app.tar');
})->desc('Compress application directory and send to destination server');

/*
 * Main task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
])->desc('Deploy your project');

task('chown', function () {
    $result = run('sudo chown -R deploy:root {{release_path}}/');
});

task('configenv', function () {
    run('sudo chmod +x {{release_path}}/configenv.sh');
    cd('{{release_path}}');
    run('sudo ./configenv.sh');
    run('sudo systemctl restart nginx');
});

task('configenv-prod', function () {
    run('sudo chmod +x {{release_path}}/configenv-prod.sh');
    cd('{{release_path}}');
    run('sudo ./configenv-prod.sh');
    run('sudo systemctl restart nginx');
});

task('getlog', function () {
    cd('{{release_path}}');
    $result = run('sudo tail var/logs/prod.log');
    writeln("$result");
});

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
