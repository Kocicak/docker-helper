#Doker skript

_Doker_ je bash skript, který zjednodušuje práci s dockerem. Automatizuje několik docker a docker-compose příkazů.

## Instalace

Stačí ho nalinkovat někam do `PATH`, např:

```shell script
ln -s /home/franta/docker-helper/scripts/doker /usr/local/bin/doker
```
_Cílový název příkazu lze samozřejmě měnit, záleží na pojmenování linku._

Ujistěte se, že je spustitelný:

```shell script
chmod +x /home/franta/docker-helper/scripts/doker
``` 

## Nezávislé stacky, síť, proxy

Aby se jednotlivé kontejnery nepraly s porty, používá se proxy a porty jednotlivých kontejnerů se nepublikují - přistupuje k nim pouze proxy.

V tomto projektu se používá proxy [traefik](https://containo.us/traefik/).

Protože si ale jednotlivé stacky vytváří své vlastní docker sítě, je potřeba je všechny spojit do jedné, ať na sebe navzájem vidí.
Výchozí docker síť "bridge" však toto neumí, proto se musí vytvořit nová - tento helper využívá jméno sítě `dev`.
Stačí ji vytvořit příkazem:

```shell script
docker network create dev
```

### Připojení stacku do proxy sítě


Na konec souboru `docker-compose.yml` přidat následující řádky:

```yaml
networks:
  default:
    external:
      name: dev
```
_Název sítě lze zvolit jakýkoliv, není nutné používat dev._

Na většině systémů funguje doménové jméno *.localhost, je vhodné jeho použití pro nastavení proxy.
Ke kontejnerům v `docker-compose.yml` souboru je vhodné zrušit vypublikování portů a nastavit proxy:

```yaml
version: "3"
services:
  my-container:
    # ...
    labels:
      - traefik.http.routers.my-container.rule=Host(`my_host.docker.localhost`)
```

Názvy _my_service_ odpovídá názvu service z docker-compose.yml a _my_host_ odpovídá jménu, které se bude zadávat do prohlížeče.

Pokud kontejner nepoužívá port 80, ale jiným je potřeba přidat label:
```yaml
version: "3"
services:
  my-container:
    # ...
    labels:
      - traefik.http.routers.my-container.rule=Host(`mydomain.com`)
      # Tell Traefik to use the port 12345 to connect to `my-container`
      - traefik.http.services.my-service.loadbalancer.server.port=12345
```

### Stacky nezávislé na projektech

Pokud chcete používat stacky nezávislé na projektech, je potřeba mít ENV proměnnou `DOCKER_STACK_DIR`.
V té uloženou cestu k jednotlivým "stackům".
 Stačí tedy do _~/.bashrc_ (nebo _~/.zshrc_ podle shellu) přidat řádek:

```shell script
export DOCKER_STACK_DIR=~/docker-helper/compose
```

Jeden takový stack "base" je připraven. Obsahuje:
- traefik proxy
- mysql - mariadb 10.2
- adminer - správa db
- [mailhog](https://github.com/mailhog/MailHog) - vývojový SMTP server

#### Spojení mezi jednotlivými kontejnery

Kontejnery budou v síti `dev` vidět na ostatní podle jejich jména. Proto pokud v docker-compose.yml bude toto nastavení:

```yaml
version: '3'
services:
  adminer:
    container_name: adminer

  mysql:
    container_name: mysql
```

Tak z kontejneru _adminer_ se půjde dostat do databáze při použití jména serveru _mysql_.
Celé doménové jméno je _nazev_kontejneru.nazev_site_, neboli v tomto případě _mysql.dev_.




## Příkazy



- `doker start <stack>`
  - spustí stack na pozadí.
- `doker startf <stack>`
  - spuští stack na popředí
- `doker stop <stack>`
  - zastaví běžící stack
- `doker update <stack>`
  - aktualizuje image ze stacku
  - pouští příkazy `docker-compose pull` (pro image z docker hubu) a `docker-compose build --pull` (pro image z lokalniho dockerfilu)
- `doker bash <kontejner>`
- `doker sh <kontejner>`
  - přejde do shellu v kontejneru (v alpinu většinou není bash, tam se použije sh)
  - stačí zadat část názvu - celý kontejner bude vyhledán. Pokud se kontejner jmenuje "muj_kontejner_php_fpm", stačí zadat příkaz `doker bash php`
  - pouští příkaz `docker exec -it bash \<kontejner\_id\>
