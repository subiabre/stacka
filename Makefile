project_dir := $(strip $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))))

install:
	mkdir -p ./build
	cp ./bin/docker ./build/bin
	sed -i 's_docker-compose.yml_$(project_dir)/docker-compose.yml_g' ./build/bin

	sudo ln -s $(project_dir)/build/bin /usr/local/bin/stacka
	sudo chmod +x /usr/local/bin/stacka

	stacka install

uninstall:
	sudo rm /usr/local/bin/stacka ./build/bin
