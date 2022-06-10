install:
	sudo ln -s $(strip $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))))/bin/docker /usr/local/bin/stacka
	sudo chmod +x /usr/local/bin/stacka

uninstall:
	sudo rm /usr/local/bin/stacka
