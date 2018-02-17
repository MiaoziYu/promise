from fabric.api import local, env, cd, run
from fabric.colors import green

env.use_ssh_config = True
env.hosts = ("linode", )

def deploy(branch="develop"):
    local("pwd && git status")
    with cd('/srv/www/promise'):
        print(green("Pulling code"))
        run("git pull")

        print(green("Checking out branch %s" % (branch, )))
        run("git checkout " + branch)

        print(green("Running composer install"))
        run("composer install")

        print(green("Npm install"))
        run("npm i")

        print(green("Npm build"))
        run("npm run prod")

        print(green("Deployment finished"))

