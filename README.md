# getlaminas.org

Source code for the getlaminas.org website.

## Testing

To test the file, use the provided [docker-compose
configuration](docker-compose.yml):

```bash
$ docker-compose build
$ docker-compose up
# browse to http://localhost:9000
```

## Building images

To build the image:

```bash
$ make image # or just "make"
```

To push the image to the Docker Hub:

```bash
$ make hub
```
