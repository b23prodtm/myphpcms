# INSTALLATION
Docker :whale: is able to use web server containers in local (VM/Vbox) or remote (Cloud) machine cluster (Kubernetes).  
A typical install script could look like the following script, for instance edit a file :

	#!/usr/bin/env bash

	cd myphpcms
	git pull
	git submodule update --init --recursive
	./docker-compose-alias.sh -dns=domain.com -S -p=sqlrootpassword -t=testpassword -v $*
	cd ..

If you saved the file as _startup.sh_  In bash follows : ```startup.sh --build -d up``` You can add more docker-compose parameters as arguments.
Docker builds up a new container and pushes it in registry.
It will eventually run the container as the startup script succeeds.

## Circle CI, Docker Local testing
The current project is a full php with mariadb container for the Docker Virtual Machine (VM) manager, or Docker-CE, or even a ```Dockerfile``` compatible container interface. We choose Circle CI because it's able to achieve full remote tests with docker :whale: before we deploy to a Cloud Provider, Kubernetes Cluster, OpenShift, etc.

A local test may only run with a complete local Virtual Host (Vbox) configuration. See the requirements below.

### Simple Docker compose tests
To use the built-in CakePHP 2 interface to test with Docker Compose YAML run the test script as follows :

	test-cake.sh --docker

### Requirements
- Broadband Internet access to the Worldwide Web, to download the packages and container images dependencies from the remote Docker registries.
- The [VBoxmanager](https://www.virtualbox.org/wiki/Downloads) package.
- The Docker VM described with the Dockerfile. >:whale: [Get Started](https://docs.docker.com/machine/get-started/) application.
- The docker:docker user SSH remote access enabled (Ask the system administrator to enable Remote Session Accounts with Password authentification in /etc/ssh/sshd_config)
- The CircleCI Client installed in ```$PATH```. [CLI Configuration](https://circleci.com/docs/2.0/local-cli/#section=configuration) shell command line :

	curl -fLSs https://circle.ci/cli | bash

Once everything is installed, please reboot your system.

### Make local tests with CircleCI CLI
- Enable Docker VM on your system (a virtual Host is instancied)
- Shell command line : ```circleci local execute```

### Startup
Everyting is ready to launch a container in real cluster environment. The process is described further. [Kubespray with Ansible and Kubernetes cluster](https://github.com/b23prodtm/kubesrpay).

### License
   Copyright 2016 www.b23prodtm.info

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
