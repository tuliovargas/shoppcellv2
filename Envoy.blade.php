@servers(['production' => 'deployer@50.16.7.186'])

@setup
    $repository = 'git@gitlab.com:lojacelular/pdvlaravel.git';
    $prd_releases_dir = '/var/www/html/shoppcell.com.br/pdv/releases';
    $prd_app_dir = '/var/www/html/shoppcell.com.br/pdv';
    $prd_current_dir = '/var/www/html/shoppcell.com.br/pdv/current';
    $release = date('YmdHis');
    $prd_new_release_dir = $prd_releases_dir .'/'. $release;
@endsetup

@task('init', ['on' => 'production', 'confirm' => true])
if [ ! -d {{ $prd_app_dir }}/current ]; then
    cd {{ $prd_app_dir }}

    git clone {{ $repository }} --branch=master --depth=1 -q {{ $release }}
    echo "Repository cloned"

    mv {{ $release }}/storage {{ $prd_app_dir }}/storage
    ln -nfs {{ $prd_app_dir }}/storage {{ $release }}/storage
    ln -nfs {{ $prd_app_dir }}/storage/app/public {{ $release }}/public/storage
    echo "Storage directory set up!"

    cp {{ $release }}/env.example {{ $prd_app_dir }}/.env
    ln -nfs {{ $prd_app_dir }}/.env {{ $release }}/.env
    echo "Environment file set up"

    rm -rf {{ $release }}
    echo "Deployment path initialised. Run 'envoy run deploy' now."
else
    echo "Deployment path already initialised (current symlink exists)!"
fi
@endtask

@story('deploy_production')
    clone_repository_prd
    run_composer_prd
    update_symlinks_prd
    migrate_release
    cache_prd
    clean_old_releases
@endstory

@task('clone_repository_prd', ['on' => 'production'])
    echo "Cloning repository"
    [ -d {{ $prd_releases_dir }} ] || mkdir {{ $prd_releases_dir }}
    git clone --depth 1 {{ $repository }} {{ $prd_new_release_dir }}

@endtask

@task('run_composer_prd', ['on' => 'production'])
    echo "Starting deployment ({{ $release }})"
    cd {{ $prd_new_release_dir }}
    composer install --prefer-dist --no-scripts -q -o
    npm install

    echo 'Linking .env file'
    ln -nfs {{ $prd_app_dir }}/.env {{ $prd_new_release_dir }}/.env
    
    npm run prod
@endtask

@task('update_symlinks_prd', ['on' => 'production'])
    echo "Linking storage directory"
    rm -rf {{ $prd_new_release_dir }}/storage;
    cd {{ $prd_new_release_dir }};
    ln -nfs {{ $prd_app_dir }}/storage {{ $prd_new_release_dir }}/storage;
    ln -nfs {{ $prd_app_dir }}/storage/app/public {{ $prd_new_release_dir }}/public/storage

    echo 'Linking current release'
    ln -nfs {{ $prd_new_release_dir }} {{ $prd_app_dir }}/current
@endtask

@task('cache_prd', ['on' => 'production'])
    echo "Building cache"

    php {{ $prd_new_release_dir }}/artisan route:cache
    php {{ $prd_new_release_dir }}/artisan config:cache
    php {{ $prd_new_release_dir }}/artisan view:cache
@endtask

@task('clean_old_releases', ['on' => 'production'])
    # Delete all but the 5 most recent releases
    echo "Cleaning old releases"
    cd {{ $prd_releases_dir }}
    ls -dt {{ $prd_releases_dir }}/* | tail -n +6 | xargs -d "\n" rm -rf;
@endtask

@task('migrate_release', ['on' => 'production', 'confirm' => false])
    echo "Running migrations"
    php {{ $prd_new_release_dir }}/artisan migrate --force
@endtask

@task('migrate', ['on' => 'production', 'confirm' => true])
    echo "Running migrations"
    php {{ $prd_current_dir }}/artisan migrate --force
@endtask

@task('migrate_rollback', ['on' => 'production', 'confirm' => true])
    echo "Rolling back migrations"
    php {{ $prd_current_dir }}/artisan migrate:rollback --force
@endtask

@task('migrate_status', ['on' => 'production'])
    php {{ $prd_current_dir }}/artisan migrate:status
@endtask

@finished
    echo "Envoy deployment script finished.\r\n";
@endfinished

