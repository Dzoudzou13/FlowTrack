# FlowTrack: Vysvetlenie Aktuálneho Kódu

Tento dokument slúži ako stručná príprava na obhajobu aktuálnej verzie projektu. Popisuje, čo je v projekte už implementované, ako je rozdelená architektúra a akú úlohu majú hlavné súbory.

## 1. Čo projekt aktuálne obsahuje

Aktuálna verzia projektu obsahuje:

- vlastnú architektúru v čistom PHP bez frameworku,
- jeden hlavný vstupný bod aplikácie,
- jednoduchý vlastný router,
- rozdelenie kódu na logiku a template súbory,
- prihlasovaciu a registračnú obrazovku,
- základné pripojenie na MySQL cez PDO,
- placeholder dashboard route.

Zatiaľ ešte nie je dokončené:

- skutočné spracovanie registrácie,
- skutočné prihlásenie používateľa,
- session kontrola prístupu na dashboard,
- zapisovanie a čítanie používateľov z databázy.

## 2. Architektúra projektu

Projekt je rozdelený na tieto časti:

- `app/` obsahuje logiku aplikácie,
- `config/` obsahuje konfiguráciu,
- `templates/` obsahuje HTML/PHP views,
- `public/` obsahuje verejne dostupné súbory,
- `routes.php` definuje route mapu aplikácie.

Takéto rozdelenie je dôležité preto, aby nebola logika miešaná priamo do verejne dostupných stránok.

## 3. Hlavný vstupný bod aplikácie

Súbor:

- `/Applications/MAMP/htdocs/FlowTrack/public/index.php`

Tento súbor funguje ako front controller. To znamená, že všetky požiadavky prechádzajú najprv cez neho.

Jeho úlohy sú:

1. zapnúť zobrazenie PHP chýb počas vývoja,
2. spustiť session,
3. načítať pomocné funkcie,
4. načítať definované routy,
5. zavolať router a odovzdať mu aktuálnu URL a HTTP metódu.

Vďaka tomu nemám v projekte samostatné verejné súbory typu `login.php`, `register.php`, `dashboard.php`, ale používam route-based prístup podobný frameworkom.

## 4. Pomocné funkcie

Súbor:

- `/Applications/MAMP/htdocs/FlowTrack/app/functions.php`

Tento súbor obsahuje:

- vlastný autoload pre triedy z namespace `App\`,
- helper `base_path()` na zostavenie absolútnej cesty v projekte,
- helper `template_path()` na načítanie template súborov,
- helper `config_path()` na načítanie config súborov,
- helper `config()` na čítanie konfigurácie,
- helper `app_url()` na tvorbu interných URL,
- helper `asset()` na tvorbu ciest k CSS a JS,
- helper `redirect()` na presmerovanie používateľa.

Tieto funkcie zjednodušujú prácu a znižujú opakovanie kódu.

## 5. Router

Súbory:

- `/Applications/MAMP/htdocs/FlowTrack/routes.php`
- `/Applications/MAMP/htdocs/FlowTrack/app/core/Router.php`

`routes.php` slúži ako centrálne miesto, kde sa definujú route pravidlá.

Momentálne sú definované route:

- `/` -> presmerovanie na `/login`
- `/login` -> prihlasovacia stránka
- `/register` -> registračná stránka
- `/dashboard` -> dashboard placeholder

Trieda `Router`:

- ukladá GET a POST route,
- normalizuje URL,
- vyhľadá správnu akciu,
- zavolá controller alebo callback,
- pri neexistujúcej route vráti 404.

Toto riešenie je jednoduché, ale pre školský projekt plne obhájiteľné, pretože ukazuje, že routing je urobený vlastnou logikou a nie cez framework.

## 6. Controllery

Súbory:

- `/Applications/MAMP/htdocs/FlowTrack/app/controllers/AuthController.php`
- `/Applications/MAMP/htdocs/FlowTrack/app/controllers/DashboardController.php`

Controller slúži ako prostredník medzi routou a view.

`AuthController`:

- zobrazuje login page,
- zobrazuje register page,
- odovzdáva template súborom hodnoty ako `pageTitle` a `activeTab`.

`DashboardController`:

- zatiaľ len zobrazuje dashboard placeholder.

Týmto spôsobom nie je HTML logika v routeri a router zostáva čistý.

## 7. View vrstva

Súbory:

- `/Applications/MAMP/htdocs/FlowTrack/app/core/View.php`
- `/Applications/MAMP/htdocs/FlowTrack/templates/...`

Trieda `View` načíta požadovaný template a odovzdá mu dáta.

Templates sú rozdelené na:

- `templates/auth/login.php`
- `templates/auth/register.php`
- `templates/dashboard/index.php`
- `templates/partials/header.php`
- `templates/partials/footer.php`
- `templates/partials/auth-screen.php`

`auth-screen.php` je zdieľaný partial pre login a register, čo znižuje duplicitu kódu.

## 8. Databázové pripojenie

Súbory:

- `/Applications/MAMP/htdocs/FlowTrack/config/database.php`
- `/Applications/MAMP/htdocs/FlowTrack/app/core/Database.php`

Konfigurácia databázy je oddelená od logiky.

Trieda `Database`:

- používa PDO,
- vytvára jedno centrálne pripojenie,
- skladá DSN z config hodnôt,
- nastavuje `ERRMODE_EXCEPTION`,
- nastavuje `FETCH_ASSOC`.

Toto je vhodnejšie riešenie než písať databázové pripojenie priamo do každej stránky.

## 9. Prečo je to OOP

Projekt spĺňa OOP prístup, pretože:

- logika je rozdelená do tried (`Router`, `Controller`, `View`, `Database`, controllery),
- triedy majú jasne oddelené zodpovednosti,
- controller dedí zo spoločnej abstraktnej triedy,
- databázové pripojenie je zapuzdrené v samostatnej triede.

## 10. Čo povedať pri obhajobe

Pri obhajobe môžeš popísať projekt takto:

1. Projekt je vytvorený v čistom PHP bez frameworku.
2. Použil som vlastnú jednoduchú MVC-like architektúru.
3. `public/index.php` funguje ako front controller.
4. Routing riešim vlastnou triedou `Router`.
5. HTML som oddelil do `templates`, aby nebola logika miešaná so zobrazením.
6. Databázu pripájam cez PDO a samostatnú triedu `Database`.
7. Login a register sú pripravené vizuálne aj architektonicky, ďalší krok je spracovanie formulárov a session autentifikácia.

## 11. Najbližší ďalší krok v projekte

Ďalšia implementácia by mala byť:

- POST registrácia používateľa,
- validácia vstupov,
- hashovanie hesla cez `password_hash()`,
- uloženie používateľa do MySQL,
- login cez `password_verify()`,
- session autentifikácia,
- ochrana dashboard route.
