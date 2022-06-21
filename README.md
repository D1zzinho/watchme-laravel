## Info:
1. Laravel [ver soon]
2. MariaDB [ver soon]
3. Nginx [ver soon]
4. PHP v8.1.6 + nodejs v12.22.5 + npm v7.5.2
5. Vue.js [ver soon]

## Jak zacząć:
###### W projekcie:
1. `git remote add origin https://github.com/D1zzinho/planning-poker.git`
2. `git fetch --all`
3. `git checkout master`
4. `git pull`
5. `git checkout develop`
6. `git pull`
7. Skopiować **.env.example** i przekształcić w **.env**.
8. Dane do połączenia z db: <br>
   `DB_CONNECTION=postgres` <br>
   `DB_HOST=db` <br>
   `DB_PORT=5432` <br>
   `DB_DATABASE=poker` <br>
   `DB_USERNAME=root` <br>
   `DB_PASSWORD=root` <br>

## Następnie:
1. Przez git bash w katalogu z projektem: `docker-compose up -d --build`
2. Sprawdzić nazwę kontenera z php przez `docker ps`
3. Wejście do basha kontenera z php: `docker exec -it NAZWA_KONTENERA_PHP bash`
4. `composer install`
5. `npm install`
6. `php artisan key:generate`
7. `php artisan migrate`
8. `php artisan view:clear`
9. `php artisan optimize`
