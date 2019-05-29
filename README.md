# getlaminas.org

Source code for the getlaminas.org website.

## Testing

To test the application, use the provided [docker-compose
configuration](docker-compose.yml):

```bash
$ docker-compose build
$ docker-compose up
# browse to http://localhost:9000
```

## Testing CSS

The above commands will launch the application. If you want to work on CSS
during that time, you will need to have node, npm, and gulp installed on your
host machine. Once they are:

```bash
$ cd bootstrap
$ npm i
$ gulp
```

From there, any changes to CSS will be propagated to the application, and
reloading the page in the browser will show the changes.

## Building images

To build the image:

```bash
$ make image # or just "make"
```

To push the image to the Docker Hub:

```bash
$ make hub
```
