# WhatsApp web php client

A client for WhatsApp web in PHP

## Firts run


Copy the file [`.env.example`](.env.example) to `.env` and put your Telegram Bot Token and the id of chat with your bot and go up the containers:

```bash
docker-compose up -d
docker-compose exec php7 ./bin/build
```

## Run

```bash
docker-compose up -d
```

### Example of use

Run this command and read the content of file [example.php](example.php):
```bash
docker-compose exec php7 php example.php
```

* List running browsers:

http://localhost:4443/grid/console

* View the browser:

Open your VNC client and access the address localhost:port, the port is the number of port exposed in file docker-compose.yml for the choosed browser to use. Example: to see Firefox access localhost:5901, to see Chrome access localhost:5900.

Default password is: **secret**

## Suggestions of new features

* [ ] Tests. Sugestions:
  * PHPUnit
  * PHPCS
  * PHPStan
  * Psalm
  * Phan
* [x] Send qrcode to a Telegram bot
* [ ] Hook to intercept all messages and send to Telegram Bot in conversation with predefined Telegram user
* [ ] Respond message in Telegram bot and forward the response to WhatsApp contact
* [ ] Define default reply message every time when receive new message in WhatsApp
* [ ] Identify when WhatsApp is offline and notify to user in Telegram Group to open the Whatsapp Client in cellphone
* [ ] Multiple sessions
