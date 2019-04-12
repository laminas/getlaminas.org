# getlaminas.org Makefile
#
# Create and tag the various containers that make up the stack.

VERSION := $(shell date +%Y%m%d%H%M)

.PHONY : all image hub

all: image

image:
	@echo "Creating site container"
	@echo "- Building container"
	- docker build -t getlaminas -f ./Dockerfile .
	@echo "- Tagging image"
	- docker tag getlaminas:latest mwop/getlaminas:$(VERSION)

hub: image
	@echo "- Pushing image to hub"
	- docker push mwop/getlaminas:$(VERSION)
