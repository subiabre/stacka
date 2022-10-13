project_dir := $(strip $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))))

.PHONY: bin

bin:
	mkdir -p ./build
	cp ./bin/docker ./build/docker
	sed -i 's_docker-compose.yml_$(project_dir)/docker-compose.yml_g' ./build/docker

link: bin
	sudo ln -s $(project_dir)/build/docker /usr/local/bin/stacka
	sudo chmod +x /usr/local/bin/stacka

unlink:
	sudo rm /usr/local/bin/stacka

install: link
	stacka install
