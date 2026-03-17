# PizzaShop

## Przegląd
To repozytorium zawiera aplikację do zamawiania pizzy, składającą się z dwóch głównych komponentów:

- **Backend** – zbudowany w PHP (framework Slim), udostępniający API REST. Backend samodzielnie oblicza ceny produktów na podstawie bazy danych (nie ufa danym z frontendu) oraz waliduje obecność wymaganych pól (e-mail, adres).
- **Frontend** – nowoczesna aplikacja Vue 3 + PrimeVue, która umożliwia przeglądanie menu, zarządzanie koszykiem oraz śledzenie czasu oczekiwania w czasie rzeczywistym.

## Uruchamianie lokalne
Aplikacja jest w pełni skonteneryzowana i gotowa do uruchomienia na dowolnym komputerze z zainstalowanym Dockerem.

```bash
# Uruchomienie aplikacji:
docker compose up
```

Dostęp do usług:
- **Frontend:** [http://localhost:3000](http://localhost:3000)
- **Backend (API):** [http://localhost:8080/api/items](http://localhost:8080/api/items)

## Logika biznesowa i funkcjonalności
- **Czas oczekiwania (Waiting Badge):** Każde nowe zamówienie dodaje dokładnie 10 minut do przewidywanego czasu oczekiwania. Licznik odświeża się automatycznie co 1 minutę oraz natychmiast po złożeniu nowego zamówienia.
- **Realistyczny Worker:** W tle działa proces (worker), który symuluje przygotowanie pizzy. Po otrzymaniu zamówienia, worker odczekuje dokładnie 10 minut (czas pieczenia), zanim oznaczy je jako zakończone i usunie z kolejki czasu oczekiwania.
- **Bezpieczeństwo:** Ceny są pobierane i sumowane na backendzie, co zapobiega manipulacjom cenowym ze strony użytkownika.
- **Baza danych:** MariaDB jest automatycznie inicjowana przy pierwszym uruchomieniu danymi z pliku `init.sql`.

## Struktura projektu
- `.data/pizza/backend/` – Kod źródłowy API PHP oraz skrypt startowy z workerem.
- `.data/pizza/frontend/` – Kod źródłowy aplikacji Vue.
- `.data/pizza/db/init/` – Skrypty inicjujące strukturę i dane bazy danych.

 