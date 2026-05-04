# getlaminas.org

> ## 🇷🇺 Русским гражданам
>
> Мы, участники Laminas, родились и живем в разных странах. У многих из нас есть друзья, родственники и коллеги как в России, так и в Украине. Некоторые из нас родились в России. Некоторые из нас живут в России. У некоторых бабушки и дедушки сражались с фашистами во Второй мировой войне. Здесь никто не поддерживает фашизм.
>
> У одного из нас есть украинская родственница, которая спаслась из дома вместе с сыном. Поезд задержался из-за бомбежки на дороге впереди. У нас есть друзья, которые прячутся в бомбоубежищах. Мы с тревогой ждем весточки от них после воздушных налетов, которые беспорядочно наносят удары и попадают по больницам, школам, детским садам и домам. Мы не берем это из каких-либо СМИ. Мы наблюдаем это напрямую.
>
> Вы доверяете нам достаточно, чтоб использовать наши программы, и мы просим вас довериться нам вновь. Мы нуждаемся в помощи. Выходите и протестуйте против этой бесполезной войны. Остановите кровопролитие. Скажите "Нет войне!"
>
> ## 🇺🇸 To Citizens of Russia
>
> We at Laminas come from all over the world. Many of us have friends, family and colleagues in both Russia and Ukraine. Some of us were born in Russia. Some of us currently live in Russia. Some have grandparents who fought Nazis in World War II. Nobody here supports fascism.
>
> One team member has a Ukrainian relative who fled her home with her son. The train was delayed due to bombing on the road ahead. We have friends who are hiding in bomb shelters. We anxiously follow up on them after the air raids, which indiscriminately fire at hospitals, schools, kindergartens and houses. We're not taking this from any media. These are our actual experiences.
>
> You trust us enough to use our software. We ask that you trust us to say the truth on this. We need your help. Go out and protest this unnecessary war. Stop the bloodshed. Say "stop the war!"

Source code for the getlaminas.org website.

## Testing

First, create a `.env` file in the root directory with contents similar to the
following:

```env
RELEASE_FEED_TOKEN=aaaabbbbccccddddeeeeffffgggg0000
```

Second, install dependencies:

```bash
$ composer install
```

Third, put the application in development mode:

```bash
$ ./vendor/bin/laminas-development-mode enable
```

Fourth, prepare the blog and security announcements:

```bash
$ mkdir -p var/blog/feeds
$ mkdir -p public/js
$ composer build
```

Fifth, build the integrations database

```bash
$ mkdir -p data/integration/database
$ mkdir -p public/images/integrations
$ ./vendor/bin/laminas integration:create-db --github-token=<github_token> [--force-rebuild]
```

Finally, use the provided [docker-compose configuration](docker-compose.yml):

```bash
$ docker-compose build
$ docker-compose up
# browse to http://localhost:8080
```

## Testing CSS

The above commands will launch the application. If you want to work on CSS
during that time, you will need to have node, npm, and gulp installed on your
host machine. Once they are:

```bash
# Because files are copied from the container, we need to make them writable:
$ sudo chmod a+rw data/assets.json public/css/*.css
# Now we can install dependencies and start watching for changes:
$ cd bootstrap
$ npm i
$ gulp
```

From there, any changes to CSS will be propagated to the application, and
reloading the page in the browser will show the changes.

Alternately, you can use the following command to rebuild the base docker
container:

```bash
$ docker-compose build php
```

## Adding blog entries

Everyone is welcome to post a blog entry. Once submitted, it will be reviewed by our team and approved to go live or rejected.
If it's rejected, the reason for the rejection will be included, so you can update it and resubmit the post if applicable.

The submission process is described in the [ADD_BLOG_ENTRY](ADD_BLOG_ENTRY.md) file.

## Adding packages to the Laminas Integrations Page

The [ADD INTEGRATION](ADD_INTEGRATION.md) file describes the process of adding packages to the [Laminas Integrations Page](https://getlaminas.org/integrations)
