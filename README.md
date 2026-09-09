# Bet dos Sapo

Projeto inicial em Laravel 13, com Vite e Tailwind CSS.

## Requisitos

- PHP 8.3 ou superior e Composer
- Node.js e npm
- Extensao SQLite habilitada no PHP

## Preparar uma copia do repositorio

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File -Path database/database.sqlite -Force
php artisan migrate
npm.cmd ci
npm.cmd run build
```

## Executar localmente

```powershell
php artisan serve
```

Abra http://127.0.0.1:8000. Para editar os estilos com atualizacao automatica, execute `npm.cmd run dev` em outro terminal.

## Validar

```powershell
php artisan test
npm.cmd run build
```

## Branches

`main`, `Luis`, `Nelson`, `Thiago` e `Joao`.

Para trabalhar em uma branch: `git switch Luis` (ou o nome correspondente).

O arquivo `.env`, o banco SQLite, `vendor` e `node_modules` ficam apenas no ambiente local.
