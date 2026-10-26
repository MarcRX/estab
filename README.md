# eStab

Stabile Fassung dieses Einsatzführungsprogramms, startbar mit Docker.

`main` ist der stabile Stand. `develop` ist die Weiterentwicklung. Veröffentlichte Stände auf `main` tragen Tags wie `v1.0.0`.

## Start

```sh
git clone <repository-url>
cd estab
cp .env.example .env
```

In `.env` die Platzhalter für Datenbank und Anwendung setzen. Danach:

```sh
docker compose up -d --build
```

Die Oberfläche liegt unter `http://localhost/kats/`.

Datenbankzugang und Datenbankname kommen aus `.env` (`MARIADB_USER`, `MARIADB_PASSWORD`, `ESTAB_DB_HOST`). Die Datei `.env` bleibt lokal und wird nicht eingecheckt.

Einsatzordner unter `www/kats/4fdata/` entstehen beim Anlegen eines Einsatzes und gehören ebenfalls nicht ins Repository. Die Datenbank liegt im Docker-Volume `mariadb_data`.
