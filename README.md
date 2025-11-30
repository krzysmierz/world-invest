# WorldInvest - WordPress Theme

Projekt zawiera motyw potomny (Child Theme) dla WordPressa, oparty na motywie **Betheme**, przygotowany dla **WorldInvest**.

## 📁 Struktura Projektu

- `style.css` - Główny arkusz stylów motywu (skompilowany).
- `style.scss` - Plik źródłowy stylów (SASS).
- `functions.php` - Funkcje i modyfikacje PHP dla motywu.
- `screenshot.png` - Podgląd motywu w panelu administratora.
- `languages/` - Pliki tłumaczeń.

## 🚀 Instalacja

1. Upewnij się, że motyw rodzic **Betheme** jest zainstalowany.
2. Skopiuj folder z tym projektem do katalogu `wp-content/themes/` w swojej instalacji WordPress.
3. Aktywuj motyw "Betheme Child" w panelu administratora (Wygląd -> Motywy).

## 🛠 Development

Projekt wykorzystuje **SCSS**. Wszelkie zmiany w stylach powinny być wprowadzane w pliku `style.scss`, a następnie kompilowane do `style.css`.

## 📝 Uwagi

- Projekt jest motywem potomnym, co zapewnia bezpieczeństwo aktualizacji motywu głównego (Betheme).
- Wszelkie niestandardowe funkcje PHP znajdują się w `functions.php`.
